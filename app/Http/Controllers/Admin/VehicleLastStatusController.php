<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class VehicleLastStatusController extends Controller
{

  private function conditionLabels(): array
  {
    return [
      'lampu_besar', 'lampu_kota', 'lampu_rem', 'lampu_sein', 'lampu_mundur', 'lampu_kabin',
      'oli_mesin', 'minyak_rem', 'minyak_kopling', 'oli_hidraulic', 'exhaust_brake',
      'ban_depan', 'ban_belakang_dalam', 'ban_belakang_luar', 'ban_serep',
      'velg_ban_depan', 'velg_ban_belakang_dalam', 'velg_ban_belakang_luar', 'velg_ban_serep',
      'tekanan_angin_ban_depan', 'tekanan_angin_ban_belakang_dalam', 'tekanan_angin_ban_belakang_luar', 'tekanan_angin_ban_serep',
      'kaca_depan', 'kaca_belakang', 'kaca_samping',
      'accu', 'tutup_radiator', 'tangki_bahan_bakar', 'tutup_tangki_bahan_bakar',
      'spion', 'wiper', 'klakson', 'panel_speedometer', 'panel_bahan_bakar', 'sunvisor', 'jok',
    ];
  }

  private function conditionPercentage(object $vehicleLastStatus): ?int
  {
    $counts = collect($vehicleLastStatus)->only($this->conditionLabels())->countBy();
    $ok = (int) $counts->get(0);
    $broken = (int) $counts->get(1);
    $total = $ok + $broken;

    return $total > 0 ? (int) round(($ok / $total) * 100) : null;
  }

  public function index()
  {

    $vehicleLastStatuses = DB::table('vehicle_last_statuses')
      ->leftJoin('vehicles', 'vehicles.id', '=', 'vehicle_last_statuses.vehicle_id')
      ->leftJoin('vehicle_documents AS kir', function ($join) {
        $join->on('kir.vehicle_id', '=', 'vehicles.id');
        $join->where('kir.type', '=', 'kir');
      })
      ->leftJoin('vehicle_documents AS stnk', function ($join) {
        $join->on('stnk.vehicle_id', '=', 'vehicles.id');
        $join->where('stnk.type', '=', 'stnk');
      })
      ->get(array_merge([
        'vehicles.id',
        'license_plate',
        'vehicle_license_plate_color_id',
        'vehicles.odo',
        'vehicle_last_statuses.id AS vehicle_last_status_id',
        'maintenance_odo',
        'maintenance_date',
      ], $this->conditionLabels(), [
        DB::raw("IFNULL(lampu_besar + lampu_kota + lampu_rem + lampu_sein + lampu_mundur + lampu_kabin + lampu_senter, 0) AS total_broken_lamp"),
        DB::raw("IFNULL(kaca_depan + kaca_samping + kaca_belakang, 0) AS total_broken_glass"),
        DB::raw(
          "IFNULL(ban_depan + ban_belakang_dalam + ban_belakang_luar + ban_serep +
        tekanan_angin_ban_depan + tekanan_angin_ban_belakang_dalam + tekanan_angin_ban_belakang_luar +
        tekanan_angin_ban_serep + velg_ban_depan + velg_ban_belakang_dalam + velg_ban_belakang_luar +
        velg_ban_serep + ganjal_ban, 0) AS total_broken_tire"
        ),
        DB::raw(
          "IFNULL(dongkrak + kunci_roda + stang_kunci_roda + pipa_bantu + kotak_p3k + apar +
        emergency_triangle + tool_kit, 0) AS total_broken_equipment"
        ),
        DB::raw(
          "IFNULL(seragam + safety_shoes + driver_license + kartu_keur + stnk + helmet +
        tatakan_menulis + ballpoint + straples, 0) AS total_broken_gear"
        ),
        DB::raw(
          "IFNULL(exhaust_brake + spion + wiper + tangki_bahan_bakar + tutup_tangki_bahan_bakar +
        tutup_radiator + accu + oli_mesin + minyak_rem + minyak_kopling + oli_hidraulic +
        klakson + panel_speedometer + panel_bahan_bakar + sunvisor + jok
        + air_conditioner, 0) AS total_broken_other"
        ),
      ]));

    $latestChecklists = Vehicle::with('latestVehicleChecklist')
      ->whereIn('id', $vehicleLastStatuses->pluck('id'))
      ->get()
      ->keyBy('id');

    $vehicleLastStatuses = $vehicleLastStatuses->map(function ($vehicleLS) use ($latestChecklists) {
      $vehicleLS->latest_checklist_id = $latestChecklists->get($vehicleLS->id)?->latestVehicleChecklist?->id;
      $vehicleLS->latest_condition_percentage = $this->conditionPercentage($vehicleLS);

      return $vehicleLS;
    });

    return view('admin.vehicle_last_status.index', [
      'title' => 'Vehicle Last Status',
      'vehicleLastStatuses' => $vehicleLastStatuses
    ]);
  }

}