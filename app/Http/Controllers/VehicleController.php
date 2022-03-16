<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Area;
use App\Models\Address;
use App\Models\Company;
use App\Models\Project;
use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleImage;
use App\Models\VehicleTowing;
use App\Models\VehicleDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\VehicleLicensePlateColor;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;

class VehicleController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $vehicles =  Vehicle::with('owner', 'project', 'vehiclesDocuments', 'area', 'vehicleVariety', 'address', 'vehicleTowing')->latest()->get();

    $vehiclesDocuments = ['stnk', 'kir'];
    $vehiclesImages = ['front', 'left', 'right', 'back'];

    $totalVehicleImage = VehicleImage::all()->count();
    $totalVehicleDocument = VehicleDocument::all()->count();
    $totalVehicle = $vehicles->count();

    $imagesMigrated = false;

    if ($totalVehicle * count($vehiclesDocuments) === $totalVehicleDocument && $totalVehicle * count($vehiclesImages) === $totalVehicleImage) {
      $imagesMigrated = true;
    }

    return view('vehicles.index', [
      'vehicles' => $vehicles,
      'imagesMigrated' => $imagesMigrated,
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('vehicles.create', [
      'vehiclesBrands' => VehicleBrand::all(),
      'areas' => Area::all(),
      'projects' => Project::all(),
      'companies' => Company::all(),
      'addresses' => Address::all(),
      'vehiclesTowings' => VehicleTowing::all(),
      'vehiclesLPColors' => VehicleLicensePlateColor::all(),
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\StoreVehicleRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreVehicleRequest $request)
  {

    try {
      // Init Configuration
      $otherTable = [
        'kir_number',
        'kir_expire',
        'kir_image',
        'stnk_number',
        'stnk_expire',
        'stnk_image',
        'front_image',
        'left_image',
        'right_image',
        'back_image',
      ];
      $timestamp = now()->timestamp;
      $documents = ['kir', 'stnk'];
      $images = ['front', 'left', 'right', 'back'];
      $imagesQuery = [];
      $documentsQuery = [];

      DB::beginTransaction();

      $newVehicle = Vehicle::create($request->safe()->except($otherTable));
      $vehicleID = $newVehicle['id'];
      $vehicleLP = str_replace(' ', '', $newVehicle['license_plate']);

      foreach ($documents as $doc) {

        if ($request->file("{$doc}_image")) {
          $fileName = "{$doc}-{$vehicleLP}-{$timestamp}.{$request->file("{$doc}_image")->extension()}";
          $imagePath = $request->file("{$doc}_image")->storeAs("{$doc}-images", $fileName, 'public');
        }

        array_push($documentsQuery, [
          'vehicle_id' => $vehicleID,
          'type' => $doc,
          'number' => $request["${doc}_number"],
          'image' => $imagePath,
          'expire' => $request["${doc}_expire"],
          'active' => $request["{$doc}_expire"] > now() ? 1 : 0,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]);
      }

      foreach ($images as $img) {

        if ($request->file("{$img}_image")) {
          $fileName = "{$img}-{$vehicleLP}-{$timestamp}.{$request->file("{$img}_image")->extension()}";
          $imagePath = $request->file("{$img}_image")->storeAs("{$img}-images", $fileName, 'public');
        }

        array_push($imagesQuery, [
          'vehicle_id' => $vehicleID,
          'type' => $img,
          'image' => $imagePath,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]);
      }

      VehicleDocument::insert($documentsQuery);
      VehicleImage::insert($imagesQuery);

      DB::commit();
      return redirect('/vehicles')->with('success', 'New vehicle has been added!');
    } catch (Exception $e) {
      DB::rollback();
      return redirect('/vehicles/create')->withInput()->with('error', $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Vehicle  $vehicle
   * @return \Illuminate\Http\Response
   */
  public function show(Vehicle $vehicle)
  {
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Vehicle  $vehicle
   * @return \Illuminate\Http\Response
   */
  public function edit(Vehicle $vehicle)
  {
    $kir = $vehicle->vehiclesDocuments->where('type', 'kir')->first();
    $stnk = $vehicle->vehiclesDocuments->where('type', 'stnk')->first();
    $front = $vehicle->vehicleImages->where('type', 'front')->first();
    $back = $vehicle->vehicleImages->where('type', 'back')->first();
    $left = $vehicle->vehicleImages->where('type', 'left')->first();
    $right = $vehicle->vehicleImages->where('type', 'right')->first();

    return view('vehicles.edit', [
      'vehiclesBrands' => VehicleBrand::all(),
      'areas' => Area::all(),
      'projects' => Project::all(),
      'companies' => Company::all(),
      'addresses' => Address::all(),
      'vehiclesTowings' => VehicleTowing::all(),
      'vehiclesLPColors' => VehicleLicensePlateColor::all(),
      'vehicle' => $vehicle,
      'stnk' => is_null($stnk) ? [] : $stnk,
      'kir' => is_null($kir) ? [] : $kir,
      'front' => $front,
      'back' => $back,
      'left' => $left,
      'right' => $right
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateVehicleRequest  $request
   * @param  \App\Models\Vehicle  $vehicle
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
  {
    try {
      // Init Configuration
      $otherTable = [
        'kir_number',
        'kir_expire',
        'kir_image',
        'stnk_number',
        'stnk_expire',
        'stnk_image',
        'front_image',
        'left_image',
        'right_image',
        'back_image',
        'vehicle_type_id',
        'vehicle_brand_id'
      ];
      $timestamp = now()->timestamp;
      $documents = ['kir', 'stnk'];
      $images = ['front', 'left', 'right', 'back'];


      $vehicleLP = str_replace(' ', '', $request->license_plate);

      DB::beginTransaction();

      Vehicle::where('id', $vehicle->id)->update($request->safe()->except($otherTable));


      foreach ($documents as $doc) {

        $document = $vehicle->vehiclesDocuments->where('type', $doc)->first();
        $imagePath = $document->image;

        if ($request->file("{$doc}_image")) {
          $fileName = "{$doc}-{$vehicleLP}-{$timestamp}.{$request->file("{$doc}_image")->extension()}";
          $imagePath = $request->file("{$doc}_image")->storeAs("{$doc}-images", $fileName, 'public');
        }

        VehicleDocument::where('id', $document->id)->update([
          'number' => $request["${doc}_number"],
          'image' => $imagePath,
          'expire' => $request["${doc}_expire"],
          'active' => $request["{$doc}_expire"] > now() ? 1 : 0,
        ]);
      }

      foreach ($images as $imgType) {

        $vehicleImage = $vehicle->vehicleImages->where('type', $imgType)->first();
        $imagePath = $vehicleImage->image;

        if ($request->file("{$imgType}_image")) {
          $fileName = "{$imgType}-{$vehicleLP}-{$timestamp}.{$request->file("{$imgType}_image")->extension()}";
          $imagePath = $request->file("{$imgType}_image")->storeAs("{$imgType}-images", $fileName, 'public');
        }

        VehicleImage::where('id', $vehicleImage->id)->update([
          'image' => $imagePath,
        ]);
      }

      DB::commit();
      return redirect("/vehicles")->with('success', 'Vehicle has been updated!');
    } catch (Exception $e) {

      DB::rollback();
      return redirect("/vehicles/{$vehicle->license_plate}/edit")->withInput()->with('error', $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Vehicle  $vehicle
   * @return \Illuminate\Http\Response
   */
  public function destroy(Vehicle $vehicle)
  {
    //
  }

  public function migrateImage()
  {
    try {
      $vehicles = Vehicle::with('vehicleImages', 'vehiclesDocuments')->get();
      $totalVehicles = $vehicles->count();
      $vehiclesDocuments = VehicleDocument::all();
      $vehiclesImages = VehicleImage::all();

      $documentsTypes = ['kir', 'stnk'];
      $imgTypes = ['front', 'left', 'right', 'back'];
      $totalDocType = count($documentsTypes);
      $totalImgType = count($imgTypes);
      $docNeedMigrate = true;
      $imgNeedMigrate = true;
      $queryImages = [];
      $queryDocuments = [];

      // Vehicles Document Full
      if ($totalVehicles * $totalDocType === $vehiclesDocuments->count()) {
        $docNeedMigrate = false;
      }

      // Vehicles Images Full
      if ($totalVehicles * $totalImgType === $vehiclesImages->count()) {
        $imgNeedMigrate = false;
      }

      if (!$imgNeedMigrate && !$docNeedMigrate) {
        return redirect('/vehicles')->with('error', 'Migration is not needed!');
      }

      foreach ($vehicles as $vehicle) {

        if ($docNeedMigrate) {
          foreach ($documentsTypes as $docType) {
            if (!$vehicle->vehiclesDocuments->contains('type', $docType)) {
              array_push($queryDocuments, [
                'vehicle_id' => $vehicle->id,
                'type' => $docType,
                'number' => 0,
                'image' => env('DEFAULT_IMAGE'),
                'expire' => now(),
                'active' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
              ]);
            }
          }
        }

        if ($imgNeedMigrate) {
          foreach ($imgTypes as $imgType) {
            if (!$vehicle->vehicleImages->contains('type', $imgType)) {
              array_push($queryImages, [
                'vehicle_id' => $vehicle->id,
                'type' => $imgType,
                'image' => env('DEFAULT_IMAGE'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
              ]);
            }
          }
        }
      }

      DB::beginTransaction();


      if ($docNeedMigrate) {
        VehicleDocument::insert($queryDocuments);
      }

      if ($imgNeedMigrate) {
        VehicleImage::insert($queryImages);
      }

      DB::commit();
      return redirect('/vehicles')->with('success', 'Migrate image completed!');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect('/vehicles')->with('error', $e->getMessage());
    }
  }
}