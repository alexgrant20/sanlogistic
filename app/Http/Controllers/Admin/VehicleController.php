<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\VehicleExport;
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
use App\Models\VehicleType;
use App\Models\VehicleVariety;
use Illuminate\Support\Facades\DB;
use App\Models\VehicleLicensePlateColor;
use App\Http\Requests\Admin\StoreVehicleRequest;
use App\Http\Requests\Admin\UpdateVehicleRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VehicleImport;
use App\Transaction\Constants\NotifactionTypeConstant;
use App\Transaction\Constants\VehicleDTConstant;

class VehicleController extends Controller
{

  public function index()
  {
    $kirQuery = DB::table('vehicle_documents AS kir')->where('type', 'kir')->select(['vehicle_id', 'type', 'expire']);
    $stnkQuery = DB::table('vehicle_documents AS stnk')->where('type', 'stnk')->select(['vehicle_id', 'type', 'expire']);

    $vehicles = DB::table('vehicles')
      ->leftJoin('companies', 'vehicles.owner_id', '=', 'companies.id')
      ->leftJoin('projects', 'vehicles.project_id', '=', 'projects.id')
      ->leftJoin('areas', 'vehicles.area_id', '=', 'areas.id')
      ->leftJoin('vehicle_varieties', 'vehicles.vehicle_variety_id', '=', 'vehicle_varieties.id')
      ->leftJoin('vehicle_types', 'vehicle_varieties.vehicle_type_id', '=', 'vehicle_types.id')
      ->leftJoin('vehicle_brands', 'vehicle_types.vehicle_brand_id', '=', 'vehicle_brands.id')
      ->leftJoin('addresses', 'vehicles.address_id', '=', 'addresses.id')
      ->leftJoin('vehicle_towings', 'vehicles.vehicle_towing_id', '=', 'vehicle_towings.id')
      ->leftJoinSub($kirQuery, 'kir', function ($join) {
        $join->on('vehicles.id', '=', 'kir.vehicle_id');
      })
      ->leftJoinSub($stnkQuery, 'stnk', function ($join) {
        $join->on('vehicles.id', '=', 'stnk.vehicle_id');
      })
      ->select(
        [
          'vehicles.id',
          'license_plate',
          'companies.name AS company_name',
          'projects.name AS project_name',
          'vehicles.status AS status',
          'vehicle_brands.name AS vehicle_brand',
          'vehicle_types.name AS vehicle_type',
          'odo',
          'kir.expire AS kir_expire',
          'stnk.expire AS stnk_expire',
        ]
      )
      ->get();

    return view('admin.vehicles.index', [
      'vehicles' => $vehicles,
      'title' => 'Vehicles',
      'importPath' => '/admin/vehicles/import/excel',
    ]);
  }

  public function create()
  {
    return view('admin.vehicles.create', [
      'vehicle' => new Vehicle(),
      'vehiclesBrands' => VehicleBrand::orderBy('name')->get(),
      'areas' => Area::orderBy('name')->get(),
      'projects' => Project::orderBy('name')->get(),
      'companies' => Company::orderBy('name')->get(),
      'addresses' => Address::orderBy('name')->get(),
      'vehiclesTowings' => VehicleTowing::orderBy('name')->get(),
      'vehiclesLPColors' => VehicleLicensePlateColor::orderBy('name')->get(),
      'title' => 'Create Vehicle',
    ]);
  }

  public function store(StoreVehicleRequest $request)
  {
    $timestamp = now()->timestamp;
    $imagesQuery = [];
    $documentsQuery = [];

    DB::beginTransaction();
    try {

      $vehicle = Vehicle::create($request->safe()->except(VehicleDTConstant::DOCUMENT_TYPE_INPUT));
      $vehicleLP = str_replace(' ', '', $vehicle->license_plate);

      foreach (VehicleDTConstant::DOCUMENT_TYPE as $docType) {
        $imageKey = "{$docType}_image";
        $imagePath = $request->hasFile($imageKey)
          ? uploadImage($request->file($imageKey), $docType, $vehicleLP, $timestamp)
          : "";

        array_push($documentsQuery, [
          'vehicle_id' => $vehicle->id,
          'type' => $docType,
          'number' => $request->get("{$docType}_number"),
          'image' => $imagePath,
          'expire' => $request->get("{$docType}_expire"),
          'active' => $request->get("{$docType}_expire") > now() ? 1 : 0,
        ]);
      }

      foreach (VehicleDTConstant::IMAGE_TYPE as $img) {

        $imageKey = "{$img}_image";
        $imagePath = $request->hasFile($imageKey)
          ? uploadImage($request->file($imageKey), $docType, $vehicleLP, $timestamp)
          : "";

        array_push($imagesQuery, [
          'vehicle_id' => $vehicle->id,
          'type' => $img,
          'image' => $imagePath,
        ]);
      }

      VehicleDocument::insert($documentsQuery);
      VehicleImage::insert($imagesQuery);
    } catch (Exception $e) {
      DB::rollback();

      return to_route('admin.vehicle.create')->withInput()
        ->with(genereateNotifaction(NotifactionTypeConstant::ERROR, 'vehicle', 'create'));
    }

    DB::commit();

    return to_route('admin.vehicle.index')
      ->with(genereateNotifaction(NotifactionTypeConstant::SUCCESS, 'vehicle', 'created'));
  }

  public function edit(Vehicle $vehicle)
  {
    $kir = $vehicle->vehiclesDocuments->where('type', 'kir')->first();
    $stnk = $vehicle->vehiclesDocuments->where('type', 'stnk')->first();
    $front = $vehicle->vehicleImages->where('type', 'front')->first();
    $back = $vehicle->vehicleImages->where('type', 'back')->first();
    $left = $vehicle->vehicleImages->where('type', 'left')->first();
    $right = $vehicle->vehicleImages->where('type', 'right')->first();

    return view('admin.vehicles.edit', [
      'vehiclesBrands' => VehicleBrand::orderBy('name')->get(),
      'areas' => Area::orderBy('name')->get(),
      'projects' => Project::orderBy('name')->get(),
      'companies' => Company::orderBy('name')->get(),
      'addresses' => Address::orderBy('name')->get(),
      'vehiclesTowings' => VehicleTowing::orderBy('name')->get(),
      'vehiclesLPColors' => VehicleLicensePlateColor::orderBy('name')->get(),
      'vehicle' => $vehicle,
      'stnk' => $stnk ?? new VehicleDocument(),
      'kir' => $kir ?? new VehicleDocument(),
      'front' => $front ?? new VehicleImage(),
      'back' => $back ?? new VehicleImage(),
      'left' => $left ?? new VehicleImage(),
      'right' => $right ?? new VehicleImage(),
      'title' => "Update Vehicle {$vehicle->license_plate}"
    ]);
  }

  public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
  {
    $timestamp = now()->timestamp;
    $vehicleLP = str_replace(' ', '', $request->license_plate);
    $documents = collect($vehicle->vehiclesDocuments->all());
    $images = collect($vehicle->vehicleImages->all());

    DB::beginTransaction();

    try {
      $vehicle->update($request->safe()->except(VehicleDTConstant::DOCUMENT_TYPE_INPUT));

      foreach (VehicleDTConstant::DOCUMENT_TYPE as $docType) {
        $document = $documents->firstWhere('type', $docType);

        $imageKey = "{$docType}_image";
        $imagePath = $request->hasFile($imageKey)
          ? uploadImage($request->file($imageKey), $docType, $vehicleLP, $timestamp)
          : $document->image ?? null;

        VehicleDocument::updateOrCreate(
          [
            'id' => $document->id ?? null,
            'vehicle_id' => $vehicle->id,
            'type' => $docType,
          ],
          [
            'number' => $request->get("{$docType}_number"),
            'expire' => $request->get("{$docType}_expire"),
            'active' => $request->get("{$docType}_expire") > now() ? 1 : 0,
            'image' => $imagePath,
          ]
        );
      }

      foreach (VehicleDTConstant::IMAGE_TYPE as $imgType) {
        $imageOld = $images->firstWhere('type', $imgType);

        $imageKey = "{$imgType}_image";
        $imagePath = $request->hasFile($imageKey)
          ? uploadImage($request->file($imageKey), $docType, $vehicleLP, $timestamp)
          : $imageOld->image ?? null;

        VehicleImage::updateOrCreate(
          [
            'id' => $imageOld->id ?? null,
            'vehicle_id' => $vehicle->id,
            'type' => $imgType,
          ],
          [
            'image' => $imagePath
          ]
        );
      }
    } catch (Exception $e) {

      dd($e->getMessage());
      DB::rollback();

      return redirect("/admin/vehicles/{$vehicle->license_plate}/edit")
        ->withInput()
        ->with(genereateNotifaction(NotifactionTypeConstant::ERROR, 'vehicle', 'updated'));
    }

    DB::commit();

    return to_route('admin.vehicle.index')
      ->with(genereateNotifaction(NotifactionTypeConstant::SUCCESS, 'vehicle', 'updated'));
  }

  public function exportExcel(Request $request)
  {
    $timestamp = now()->timestamp;
    $params = $request->input('ids');
    $ids = preg_split("/[,]/", $params);
    return Excel::download(new VehicleExport($ids), "vehicles_export_{$timestamp}.xlsx");
  }

  public function importExcel(Request $request)
  {
    $request->validate([
      'file' => 'required|mimes:csv,xls,xlsx'
    ]);
    $import = new VehicleImport;

    try {
      $file = $request->file('file')->store('file-import/vehicle/');
      $import->import($file);
    } catch (Exception $e) {
      return to_route('admin.vehicle.index')
        ->with(genereateNotifaction(NotifactionTypeConstant::ERROR, 'vehicle', 'import'));
    }

    if ($import->failures()->isNotEmpty()) {
      return back()->with('importErrorList', $import->failures());
    }

    return to_route('admin.vehicle.index')
      ->with(genereateNotifaction(NotifactionTypeConstant::SUCCESS, 'vehicle', 'imported'));
  }

  // Api Route
  public function vehicleType($id)
  {
    $data = VehicleType::where('vehicle_brand_id', $id)->get();
    return response()->json($data);
  }

  public function vehicleVariety($id)
  {
    $data = VehicleVariety::where('vehicle_type_id', $id)->get();
    return response()->json($data);
  }
}