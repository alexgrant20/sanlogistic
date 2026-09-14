<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
  use HasFactory, Blameable;

  protected $guarded = ['id'];

  public function vehiclesDocuments()
  {
    return $this->hasMany(VehicleDocument::class);
  }

  public function vehiclesLicensePlateColor()
  {
    return $this->belongsTo(VehicleLicensePlateColor::class, 'vehicle_license_plate_color_id');
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function area()
  {
    return $this->belongsTo(Area::class);
  }

  public function vehicleVariety()
  {
    return $this->belongsTo(VehicleVariety::class, 'vehicle_variety_id');
  }

  public function address()
  {
    return $this->belongsTo(Address::class);
  }

  public function owner()
  {
    return $this->belongsTo(Company::class, 'owner_id');
  }

  public function vehicleTowing()
  {
    return $this->belongsTo(VehicleTowing::class, 'vehicle_towing_id');
  }

  public function vehicleImages()
  {
    return $this->hasMany(VehicleImage::class);
  }

  public function vehicleLastStatus()
  {
    return $this->hasOne(VehicleLastStatus::class);
  }

  public function vehicleChecklist()
  {
    return $this->hasMany(VehicleChecklist::class);
  }

  public function latestVehicleChecklist()
  {
    return $this->hasOne(VehicleChecklist::class)->latestOfMany();
  }

  public function getRouteKeyName()
  {
    return 'license_plate';
  }
}