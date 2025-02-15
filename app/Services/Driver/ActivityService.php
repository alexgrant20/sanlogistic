<?php

namespace App\Services\Driver;

use App\Helpers\ActivityHelper;
use App\Interface\ActivityStatusInterface;
use App\Interface\CompanyInterface;
use App\Interface\ResponseCodeInterface;
use App\Models\Activity;
use App\Models\ActivityIncentive;
use App\Models\ActivityPayment;
use App\Models\ActivityStatus;
use App\Models\Address;
use App\Models\ErrorLog;
use App\Models\IncentiveRate;
use App\Models\User;
use App\Models\Vehicle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ActivityService implements CompanyInterface, ActivityStatusInterface, ResponseCodeInterface
{
  private $needToCaclculateIncentive = false;
  private $activityHelper;

  public function __construct()
  {
    $this->activityHelper = new ActivityHelper();
  }

  private function setActivityConfigAndGetType($activity, $arrivalType, $departureType, &$totalCustTrip)
  {
    $activityType = null;
    $driver = auth()->user()->driver;

    switch ($arrivalType) {
      case "TUJUAN PENGIRIMAN":
        $totalCustTrip += 1;

        $driver->update([
          'last_activity_id' => $activity->id,
          'total_cust_trip' => $totalCustTrip,
        ]);

        $activityType = "mdp-" . $totalCustTrip;
        break;

      case "KANTOR UTAMA":
      case "POOL":
        $activityType = "pool";
        break;

      case "STATION":
        $this->needToCaclculateIncentive = true;

        if ($departureType == "STATION") {
          $activityType = 'manuver';
        } else {
          if ($totalCustTrip != 0) {
            $driver->lastActivity->update([
              'type' => $totalCustTrip > 1 ? 'mdp-e' : 'sdp'
            ]);

            $driver->update([
              'last_activity_id' => NULL,
              'total_cust_trip' => 0,
            ]);
          }

          $activityType = 'return';
        }

        break;
      case "WORKSHOP":
        $activityType = "maintenance";
        break;

      case "PKB/SAMSAT":
        $activityType = "kir";
        break;
    }

    return $activityType;
  }

  private function getActivityFixedPayload($licensePlate, $activityPayload, $activityFiles, $additionalPayload)
  {
    ['lat' => $lat, 'lon' => $lon, 'loc' => $loc] = get_location_ngt(str_replace(' ', '', $licensePlate));

    $additionalPayload = array_merge($additionalPayload, [
      'end_lat' => $lat,
      'end_lon' => $lon,
      'end_loc' => $loc,
    ]);

    $payload = $activityPayload->merge($additionalPayload)->toArray();


    $images = collect($activityFiles);
    $timestamp = now()->timestamp;

    $listOfPath = uploadImages($images, auth()->user()->person->name, $timestamp);
    $payload = array_merge($payload, $listOfPath);

    return $payload;
  }

  public function store($request)
  {
    DB::beginTransaction();

    try {
      $user = User::lockForUpdate()->find(Auth::id());
      $vehicle = Vehicle::find($request->vehicle_id);

      $userActiveActivty = Activity::where('user_id', $user->id)
        ->status(self::STATUS_DRAFT)
        ->get();

      if ($userActiveActivty->isNotEmpty()) {
        throw new Exception(
          "User memiliki usulan aktif dengan ID: " . $userActiveActivty->pluck('id')->join(', '),
          self::ERROR_REDIRECT_INDEX
        );
      }

      $storeAdditionalPayload = [
        'do_date' => now(),
        'user_id' => $user->id,
        'project_id' => $vehicle->project_id,
      ];

      $fixedPayload = $this->getActivityFixedPayload(
        $vehicle->license_plate,
        $request->safe(),
        $request->allFiles(),
        $storeAdditionalPayload
      );

      $activity = Activity::create($fixedPayload);

      $vehicle->update([
        'last_do_number' => $request->do_number,
        'last_do_date' => now(),
      ]);

      ActivityStatus::create(['status' => 'draft', 'activity_id' => $activity->id]);
    } catch (\Exception $e) {
      DB::rollBack();
      ErrorLog::createLog($e);
      throw $e;
    }

    DB::commit();
    return $activity;
  }

  private function getActivityParentId(Activity $currentActivity)
  {
    $relatedActivity = Activity::where([
      ['user_id', Auth::id()],
      ['type', '!=', 'tbd'],
    ])
      ->latest()
      ->first();

    if (
      !in_array(@$relatedActivity->type, ['return', 'manuver']) &&
      @$relatedActivity->parent_activity_id
    ) return $relatedActivity->parent_activity_id;

    return $currentActivity->id;
  }

  public function update($request, Activity $activity)
  {
    DB::beginTransaction();

    $userValidationError = "400";

    try {
      $activity = Activity::lockForUpdate()->find($activity->id);

      $statusIsNotDraft = $activity->activityStatus->status != 'draft';

      if ($activity->parent_activity_id || $statusIsNotDraft) {
        throw new \Exception("Aktivitas Sudah Dikerjakan / Dibatalkan!", self::ERROR_REDIRECT_INDEX);
      }

      $vehicle = $activity->vehicle;
      $totalCustTrip = auth()->user()->driver->total_cust_trip;
      $arrivalType =  strtoupper(Address::find($request->arrival_location_id)->addressType->name);
      $departureType = strtoupper($activity->departureLocation->addressType->name);

      $activityType = $this->setActivityConfigAndGetType($activity, $arrivalType, $departureType, $totalCustTrip);

      $finalizedAdditionalPayload = [
        'type' => $activityType,
        'arrival_date' => now()
      ];

      $fixedPayload = $this->getActivityFixedPayload(
        $vehicle->license_plate,
        $request->safe(),
        $request->allFiles(),
        $finalizedAdditionalPayload,
      );

      $this->finalizedActivityStep($request, $activity, $fixedPayload, $totalCustTrip);
    } catch (\Exception $e) {
      DB::rollBack();

      if ($e->getCode() != $userValidationError) {
        ErrorLog::createLog($e);
      }

      throw ($e);
    }

    DB::commit();
    return true;
  }

  private function createActivityIncentiveRate($parentActivityId)
  {
    $incentiveRatePayload = $this->activityHelper->getActivityIncentiveRatePayload($parentActivityId);

    ActivityIncentive::create($incentiveRatePayload);
  }

  private function finalizedActivityStep($request, $activity, $updatedActivityPayload, $totalCustTrip)
  {
    $parentActivityId = $this->getActivityParentId($activity, $totalCustTrip);

    $updatedActivityPayload['parent_activity_id'] = $parentActivityId;

    $activity->update($updatedActivityPayload);

    $activityStatus = ActivityStatus::create([
      'status' => 'pending',
      'activity_id' => $activity->id
    ]);

    if ($this->needToCaclculateIncentive) {
      $this->createActivityIncentiveRate($parentActivityId);
    }

    if ($activity->vehicle_id) {
      $activity->vehicle->update([
        'odo' => $request->arrival_odo,
        'address_id' => $request->arrival_location_id,
      ]);
    }

    ActivityPayment::create([
      'activity_status_id' => $activityStatus->id,
      'bbm_amount' => $request->bbm_amount ?? 0,
      'toll_amount' => $request->toll_amount ?? 0,
      'parking_amount' => $request->parking_amount ?? 0,
    ]);

    auth()->user()->driver->update([
      'last_location_id' => $request->arrival_location_id
    ]);
  }

  public function storePublicTransport(Request $request)
  {
    $user = auth()->user();
    $driver = $user->driver;

    DB::beginTransaction();

    try {
      $departureLocationId = Crypt::decryptString($request->departure_location_id);

      $addressessType = Address::whereIn('id', [$departureLocationId, $request->arrival_location_id])
        ->get()
        ->mapWithKeys(function ($address) use ($departureLocationId) {
          $key = $address->id == $departureLocationId ? 'departure_location' : 'arrival_location';
          return [$key => strtoupper($address->addressType->name)];
        });

      $totalCustTrip = $driver->total_cust_trip;

      $activity = Activity::create([
        'user_id' => $user->id,
        'departure_location_id' => $departureLocationId,
        'arrival_location_id' => $request->arrival_location_id,
        'arrival_date' => now(),
        'project_id' => $user->person->project->id,
        'do_number' => 'PT',
        'do_date' => now(),
      ]);

      $activityType = $this->setActivityConfigAndGetType(
        $activity,
        $addressessType['arrival_location'],
        $addressessType['departure_location'],
        $totalCustTrip
      );

      $this->finalizedActivityStep($request, $activity, ['type' => $activityType], $totalCustTrip);
    } catch (\Exception $e) {
      DB::rollBack();
      ErrorLog::createLog($e);
      return false;
    }

    DB::commit();
    return true;
  }
}
