<?php

namespace App\Http\Controllers\Driver;

use App\Models\Activity;
use App\Models\AddressProject;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\StoreActivityRequest;
use App\Http\Requests\Driver\StorePublicTransportActivityRequest;
use App\Http\Requests\Driver\UpdateActivityRequest;
use App\Interface\ResponseCodeInterface;
use App\Models\VehicleChecklist;
use App\Models\VehicleChecklistImage;
use App\Models\VehicleLastStatus;
use App\Services\Driver\ActivityService;
use App\Transaction\Constants\NotifactionTypeConstant;
use App\Utilities\DriverUtility;
use Illuminate\Http\Request;

class ActivityController extends Controller implements ResponseCodeInterface
{
  private $activityService;
  private $driverUtility;

  public function __construct()
  {
    $this->activityService = new ActivityService();
    $this->middleware('can:activity-create');
    $this->driverUtility = new DriverUtility();
  }

  public function index()
  {
    $activities = DB::table('activities')
      ->leftJoin('activity_statuses', 'activities.activity_status_id', '=', 'activity_statuses.id')
      ->leftJoin('activity_payments', 'activity_statuses.id', '=', 'activity_payments.activity_status_id')
      ->leftJoin(DB::raw('addresses dep'), 'activities.departure_location_id', '=', 'dep.id')
      ->leftJoin(DB::raw('addresses arr'), 'activities.arrival_location_id', '=', 'arr.id')
      ->leftJoin('vehicles', 'activities.vehicle_id', '=', 'vehicles.id')
      ->where('user_id', '=', auth()->user()->id)
      ->orderByDesc('activities.created_at')
      ->selectRaw(
        'bbm_amount + toll_amount + parking_amount + load_amount + unload_amount + maintenance_amount AS total_cost, arrival_odo - departure_odo AS distance,
        activity_statuses.status, do_number, departure_date, arrival_date, dep.name AS departure_name, arr.name AS arrival_name, license_plate,
        (CASE
          WHEN activity_statuses.status = "draft" THEN "bg-warning"
          WHEN activity_statuses.status = "pending" THEN "bg-warning"
          WHEN activity_statuses.status = "approved" THEN "bg-info"
          WHEN activity_statuses.status = "paid" THEN "bg-success"
          WHEN activity_statuses.status = "rejected" THEN "bg-primary"
          END) AS activityStatusColor'
      )
      ->paginate(6);

    return view('driver.activities.index', [
      'activities' => $activities,
    ]);
  }

  public function create()
  {
    $user = auth()->user();
    $lastLocation = $this->driverUtility->getLocation($user->driver);
    $projectId = $user->person->project_id;

    $vehicles = Vehicle::where('address_id', $lastLocation->id)
      ->where('project_id', $projectId)
      ->orderBy('license_plate')
      ->get();

    $vehicleIdOnDuty = Activity::whereRelation('activityStatus', 'status', 'draft')
      ->whereIn('vehicle_id', $vehicles->pluck('id')->toArray())
      ->pluck('vehicle_id')
      ->toArray();

    $vehicles = $vehicles->whereNotIn('id', $vehicleIdOnDuty);

    return view('driver.activities.create', [
      'vehicles' => $vehicles,
      'projectId' => $projectId
    ]);
  }

  public function store(StoreActivityRequest $request)
  {
    try {
      $activity = $this->activityService->store($request);
    } catch (\Exception $e) {
      $errorCode = $e->getCode();

      $route = $errorCode == self::ERROR_REDIRECT_INDEX ? to_route('index') : back();
      $errorMessage = 'Aktivitas Gagal Dibuat, Terjadi Kesalahan!';

      return $route->with('error-swal', $errorMessage);
    }

    $request->session()->put('activity_id', $activity->id);

    $timestamp = now()->timestamp;

    $basicData = $request->validate([
      'vehicle_id' => 'required|integer',
      'lamp_notes' => 'nullable|string',
      'glass_notes' => 'nullable|string',
      'tire_notes' => 'nullable|string',
      'equipment_notes' => 'nullable|string',
      'gear_notes' => 'nullable|string',
      'other_notes' => 'nullable|string',
    ]);

    $imageData = $request->validate([
      'image_1' => 'nullable|image|mimes:png,jpg,jpeg',
      'image_2' => 'nullable|image|mimes:png,jpg,jpeg',
      'image_3' => 'nullable|image|mimes:png,jpg,jpeg',
      'image_4' => 'nullable|image|mimes:png,jpg,jpeg',
      'image_1_description' => 'nullable|string',
      'image_2_description' => 'nullable|string',
      'image_3_description' => 'nullable|string',
      'image_4_description' => 'nullable|string',
    ]);

    $vehicle = Vehicle::find($request->vehicle_id);

    $checklistData = [
      'lampu_besar' => intval($request->lampu_besar ?? 1),
      'lampu_kota' => intval($request->lampu_kota ?? 1),
      'lampu_rem' => intval($request->lampu_rem ?? 1),
      'lampu_sein' => intval($request->lampu_sein ?? 1),
      'lampu_mundur' => intval($request->lampu_mundur ?? 1),
      'lampu_kabin' => intval($request->lampu_kabin ?? 1),
      'lampu_senter' => intval($request->lampu_senter ?? 1),
      'kaca_depan' => intval($request->kaca_depan ?? 1),
      'kaca_samping' => intval($request->kaca_samping ?? 1),
      'kaca_belakang' => intval($request->kaca_belakang ?? 1),
      'ban_depan' => intval($request->ban_depan ?? 1),
      'ban_belakang_dalam' => intval($request->ban_belakang_dalam ?? 1),
      'ban_belakang_luar' => intval($request->ban_belakang_luar ?? 1),
      'ban_serep' => intval($request->ban_serep ?? 1),
      'tekanan_angin_ban_depan' => intval($request->tekanan_angin_ban_depan ?? 1),
      'tekanan_angin_ban_belakang_dalam' => intval($request->tekanan_angin_ban_belakang_dalam ?? 1),
      'tekanan_angin_ban_belakang_luar' => intval($request->tekanan_angin_ban_belakang_luar ?? 1),
      'tekanan_angin_ban_serep' => intval($request->tekanan_angin_ban_serep ?? 1),
      'velg_ban_depan' => intval($request->velg_ban_depan ?? 1),
      'velg_ban_belakang_dalam' => intval($request->velg_ban_belakang_dalam ?? 1),
      'velg_ban_belakang_luar' => intval($request->velg_ban_belakang_luar ?? 1),
      'velg_ban_serep' => intval($request->velg_ban_serep ?? 1),
      'ganjal_ban' => intval($request->ganjal_ban ?? 1),
      'dongkrak' => intval($request->dongkrak ?? 1),
      'kunci_roda' => intval($request->kunci_roda ?? 1),
      'stang_kunci_roda' => intval($request->stang_kunci_roda ?? 1),
      'pipa_bantu' => intval($request->pipa_bantu ?? 1),
      'kotak_p3k' => intval($request->kotak_p3k ?? 1),
      'apar' => intval($request->apar ?? 1),
      'emergency_triangle' => intval($request->emergency_triangle ?? 1),
      'tool_kit' => intval($request->tool_kit ?? 1),
      'seragam' => intval($request->seragam ?? 1),
      'safety_shoes' => intval($request->safety_shoes ?? 1),
      'driver_license' => intval($request->driver_license ?? 1),
      'kartu_keur' => intval($request->kartu_keur ?? 1),
      'stnk' => intval($request->stnk ?? 1),
      'helmet' => intval($request->helmet ?? 1),
      'tatakan_menulis' => intval($request->tatakan_menulis ?? 1),
      'ballpoint' => intval($request->ballpoint ?? 1),
      'straples' => intval($request->straples ?? 1),
      'exhaust_brake' => intval($request->exhaust_brake ?? 1),
      'spion' => intval($request->spion ?? 1),
      'wiper' => intval($request->wiper ?? 1),
      'tangki_bahan_bakar' => intval($request->tangki_bahan_bakar ?? 1),
      'tutup_tangki_bahan_bakar' => intval($request->tutup_tangki_bahan_bakar ?? 1),
      'tutup_radiator' => intval($request->tutup_radiator ?? 1),
      'accu' => intval($request->accu ?? 1),
      'oli_mesin' => intval($request->oli_mesin ?? 1),
      'minyak_rem' => intval($request->minyak_rem ?? 1),
      'minyak_kopling' => intval($request->minyak_kopling ?? 1),
      'oli_hidraulic' => intval($request->oli_hidraulic ?? 1),
      'klakson' => intval($request->klakson ?? 1),
      'panel_speedometer' => intval($request->panel_speedometer ?? 1),
      'panel_bahan_bakar' => intval($request->panel_bahan_bakar ?? 1),
      'sunvisor' => intval($request->sunvisor ?? 1),
      'jok' => intval($request->jok ?? 1),
      'air_conditioner' => intval($request->air_conditioner ?? 1),
      'address_id' => intval($vehicle->address_id),
      'odo' => intval($vehicle->odo),
      'user_id' => auth()->user()->id,
    ];

    $payload = array_merge($basicData, $checklistData);

    $vehicleChecklist = VehicleChecklist::create($payload);

    VehicleLastStatus::updateOrCreate(
      ['vehicle_id' => $request->vehicle_id],
      [
        'lampu_besar' => !isset($request->lampu_besar) ? 1 : DB::raw('lampu_besar'),
        'lampu_kota' => !isset($request->lampu_kota) ? 1 : DB::raw('lampu_kota'),
        'lampu_rem' => !isset($request->lampu_rem) ? 1 : DB::raw('lampu_rem'),
        'lampu_sein' => !isset($request->lampu_sein) ? 1 : DB::raw('lampu_sein'),
        'lampu_mundur' => !isset($request->lampu_mundur) ? 1 : DB::raw('lampu_mundur'),
        'lampu_kabin' => !isset($request->lampu_kabin) ? 1 : DB::raw('lampu_kabin'),
        'lampu_senter' => !isset($request->lampu_senter) ? 1 : DB::raw('lampu_senter'),
        'kaca_depan' => !isset($request->kaca_depan) ? 1 : DB::raw('kaca_depan'),
        'kaca_samping' => !isset($request->kaca_samping) ? 1 : DB::raw('kaca_samping'),
        'kaca_belakang' => !isset($request->kaca_belakang) ? 1 : DB::raw('kaca_belakang'),
        'ban_depan' => !isset($request->ban_depan) ? 1 : DB::raw('ban_depan'),
        'ban_belakang_dalam' => !isset($request->ban_belakang_dalam) ? 1 : DB::raw('ban_belakang_dalam'),
        'ban_belakang_luar' => !isset($request->ban_belakang_luar) ? 1 : DB::raw('ban_belakang_luar'),
        'ban_serep' => !isset($request->ban_serep) ? 1 : DB::raw('ban_serep'),
        'tekanan_angin_ban_depan' => !isset($request->tekanan_angin_ban_depan) ? 1 : DB::raw('tekanan_angin_ban_depan'),
        'tekanan_angin_ban_belakang_dalam' => !isset($request->tekanan_angin_ban_belakang_dalam) ? 1 : DB::raw('tekanan_angin_ban_belakang_dalam'),
        'tekanan_angin_ban_belakang_luar' => !isset($request->tekanan_angin_ban_belakang_luar) ? 1 : DB::raw('tekanan_angin_ban_belakang_luar'),
        'tekanan_angin_ban_serep' => !isset($request->tekanan_angin_ban_serep) ? 1 : DB::raw('tekanan_angin_ban_serep'),
        'velg_ban_depan' => !isset($request->velg_ban_depan) ? 1 : DB::raw('velg_ban_depan'),
        'velg_ban_belakang_dalam' => !isset($request->velg_ban_belakang_dalam) ? 1 : DB::raw('velg_ban_belakang_dalam'),
        'velg_ban_belakang_luar' => !isset($request->velg_ban_belakang_luar) ? 1 : DB::raw('velg_ban_belakang_luar'),
        'velg_ban_serep' => !isset($request->velg_ban_serep) ? 1 : DB::raw('velg_ban_serep'),
        'ganjal_ban' => !isset($request->ganjal_ban) ? 1 : DB::raw('ganjal_ban'),
        'dongkrak' => !isset($request->dongkrak) ? 1 : DB::raw('dongkrak'),
        'kunci_roda' => !isset($request->kunci_roda) ? 1 : DB::raw('kunci_roda'),
        'stang_kunci_roda' => !isset($request->stang_kunci_roda) ? 1 : DB::raw('stang_kunci_roda'),
        'pipa_bantu' => !isset($request->pipa_bantu) ? 1 : DB::raw('pipa_bantu'),
        'kotak_p3k' => !isset($request->kotak_p3k) ? 1 : DB::raw('kotak_p3k'),
        'apar' => !isset($request->apar) ? 1 : DB::raw('apar'),
        'emergency_triangle' => !isset($request->emergency_triangle) ? 1 : DB::raw('emergency_triangle'),
        'tool_kit' => !isset($request->tool_kit) ? 1 : DB::raw('tool_kit'),
        'seragam' => !isset($request->seragam) ? 1 : DB::raw('seragam'),
        'safety_shoes' => !isset($request->safety_shoes) ? 1 : DB::raw('safety_shoes'),
        'driver_license' => !isset($request->driver_license) ? 1 : DB::raw('driver_license'),
        'kartu_keur' => !isset($request->kartu_keur) ? 1 : DB::raw('kartu_keur'),
        'stnk' => !isset($request->stnk) ? 1 : DB::raw('stnk'),
        'helmet' => !isset($request->helmet) ? 1 : DB::raw('helmet'),
        'tatakan_menulis' => !isset($request->tatakan_menulis) ? 1 : DB::raw('tatakan_menulis'),
        'ballpoint' => !isset($request->ballpoint) ? 1 : DB::raw('ballpoint'),
        'straples' => !isset($request->straples) ? 1 : DB::raw('straples'),
        'exhaust_brake' => !isset($request->exhaust_brake) ? 1 : DB::raw('exhaust_brake'),
        'spion' => !isset($request->spion) ? 1 : DB::raw('spion'),
        'wiper' => !isset($request->wiper) ? 1 : DB::raw('wiper'),
        'tangki_bahan_bakar' => !isset($request->tangki_bahan_bakar) ? 1 : DB::raw('tangki_bahan_bakar'),
        'tutup_tangki_bahan_bakar' => !isset($request->tutup_tangki_bahan_bakar) ? 1 : DB::raw('tutup_tangki_bahan_bakar'),
        'tutup_radiator' => !isset($request->tutup_radiator) ? 1 : DB::raw('tutup_radiator'),
        'accu' => !isset($request->accu) ? 1 : DB::raw('accu'),
        'oli_mesin' => !isset($request->oli_mesin) ? 1 : DB::raw('oli_mesin'),
        'minyak_rem' => !isset($request->minyak_rem) ? 1 : DB::raw('minyak_rem'),
        'minyak_kopling' => !isset($request->minyak_kopling) ? 1 : DB::raw('minyak_kopling'),
        'oli_hidraulic' => !isset($request->oli_hidraulic) ? 1 : DB::raw('oli_hidraulic'),
        'klakson' => !isset($request->klakson) ? 1 : DB::raw('klakson'),
        'panel_speedometer' => !isset($request->panel_speedometer) ? 1 : DB::raw('panel_speedometer'),
        'panel_bahan_bakar' => !isset($request->panel_bahan_bakar) ? 1 : DB::raw('panel_bahan_bakar'),
        'sunvisor' => !isset($request->sunvisor) ? 1 : DB::raw('sunvisor'),
        'jok' => !isset($request->jok) ? 1 : DB::raw('jok'),
        'air_conditioner' => !isset($request->air_conditioner) ? 1 : DB::raw('air_conditioner'),
        'address_id' => intval($vehicle->address_id),
        'odo' => intval($vehicle->odo),
      ]
    );

    for ($i = 1; $i <= 4; $i++) {
      if (!array_key_exists("image_$i", $imageData)) break;
      $vehicleChecklistImage = new VehicleChecklistImage();
      $vehicleChecklistImage->image = uploadImage($imageData["image_$i"], 'checklist', auth()->user()->person->name . "-image_$i", $timestamp);
      $vehicleChecklistImage->description = $imageData["image_" . $i . "_description"];
      $vehicleChecklistImage->vehicle_checklist_id = $vehicleChecklist->id;
      $vehicleChecklistImage->save();
    }

    return to_route('index')->with('success-swal', 'Aktivitas Berhasil Dibuat');
  }

  public function edit(Activity $activity)
  {
    return view('driver.activities.edit', [
      'activity' => $activity,
      'arrival_addresses' => AddressProject::where('address_id', '!=', $activity->departure_location_id)
        ->where('project_id', $activity->project_id)
        ->with('address')
        ->get()
        ->sortBy('address.name')
    ]);
  }

  public function update(UpdateActivityRequest $request, Activity $activity)
  {
    try {
      $this->activityService->update($request, $activity);

      $request->session()->forget('activity_id');
    } catch (\Exception $e) {
      $errorMessage = "Aktivitas Gagal Diselesaikan, Silahkan Hubungi Admin!";

      if($e->getCode() == self::ERROR_REDIRECT_INDEX)
      {
        $errorMessage = $e->getMessage();

        $request->session()->forget('activity_id');
      }

      return to_route('index')->with('error-swal', $errorMessage);
    }

    return to_route('index')->with('success-swal', 'Aktivitas Berhasil Diselesaikan');
  }

  public function createPublicTransport()
  {
    $user = auth()->user();

    $departureAddress = $this->driverUtility->getLocation($user->driver);

    $arrivalAddresses =  AddressProject::where([
      ['project_id', $user->person->project_id],
      ['address_id', '!=', $departureAddress->id],
    ])
      ->with('address')
      ->get()
      ->sortBy('address.name');

    return view('driver.activities.create-public-transport', [
      'arrivalAddresses' => $arrivalAddresses,
      'departureAddress' => $departureAddress,
    ]);
  }

  public function storePublicTransport(StorePublicTransportActivityRequest $request)
  {
    $isStored = $this->activityService->storePublicTransport($request);

    if (!$isStored) {
      return back()->with('error-swal', 'Aktivitas Gagal Dibuat');
    }

    return to_route('index')->with('success-swal', 'Aktivitas Sukses Dibuat!');
  }
}
