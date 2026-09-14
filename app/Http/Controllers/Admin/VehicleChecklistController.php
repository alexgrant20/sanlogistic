<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleChecklist;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleChecklistController extends Controller
{
  private function conditionLabelGroups(): array
  {
    return [
      'lamp' => ['lampu_besar', 'lampu_kota', 'lampu_rem', 'lampu_sein', 'lampu_mundur', 'lampu_kabin'],
      'oil' => ['oli_mesin', 'minyak_rem', 'minyak_kopling', 'oli_hidraulic', 'exhaust_brake'],
      'tire' => ['ban_depan', 'ban_belakang_dalam', 'ban_belakang_luar', 'ban_serep'],
      'velg' => ['velg_ban_depan', 'velg_ban_belakang_dalam', 'velg_ban_belakang_luar', 'velg_ban_serep'],
      'tirePreasure' => ['tekanan_angin_ban_depan', 'tekanan_angin_ban_belakang_dalam', 'tekanan_angin_ban_belakang_luar', 'tekanan_angin_ban_serep'],
      'glass' => ['kaca_depan', 'kaca_belakang', 'kaca_samping'],
      'otherOutside' => ['accu', 'tutup_radiator', 'tangki_bahan_bakar', 'tutup_tangki_bahan_bakar'],
      'otherInside' => ['spion', 'wiper', 'klakson', 'panel_speedometer', 'panel_bahan_bakar', 'sunvisor', 'jok'],
    ];
  }

  private function conditionPercentage(VehicleChecklist $item, array $labels): int
  {
    $uniqueVal = collect($item)->only($labels)->countBy();
    $ok = (int) $uniqueVal->get(0);
    $broken = (int) $uniqueVal->get(1);

    return $ok + $broken > 0 ? (int) round(($ok / ($ok + $broken)) * 100) : 0;
  }

  private function allConditionLabels(): array
  {
    return array_merge(...array_values($this->conditionLabelGroups()));
  }

  private function buildShowData(VehicleChecklist $vehicleChecklist)
  {
    $labelGroups = $this->conditionLabelGroups();
    $lampLabel = $labelGroups['lamp'];
    $oilLabel = $labelGroups['oil'];
    $tireLabel = $labelGroups['tire'];
    $velgLabel = $labelGroups['velg'];
    $tirePreasureLabel = $labelGroups['tirePreasure'];
    $glassLabel = $labelGroups['glass'];
    $otherOutsideLabel = $labelGroups['otherOutside'];
    $otherInsideLabel = $labelGroups['otherInside'];

    $vehicleChecklist->load('user.person');
    $vehicleChecklist__ori = $vehicleChecklist;
    $vehicle = $vehicleChecklist->vehicle;
    $vehicleChecklist = collect($vehicleChecklist);

    $lamp = $vehicleChecklist->only($lampLabel);
    $oil = $vehicleChecklist->only($oilLabel);
    $tire = $vehicleChecklist->only($tireLabel);
    $velg = $vehicleChecklist->only($velgLabel);
    $tirePreasure = $vehicleChecklist->only($tirePreasureLabel);
    $glass = $vehicleChecklist->only($glassLabel);
    $otherOutside = $vehicleChecklist->only($otherOutsideLabel);
    $otherInside = $vehicleChecklist->only($otherInsideLabel);

    $lampCount = $lamp->countBy();
    $oilCount = $oil->countBy();
    $tireCount = $tire->countBy();
    $velgCount = $velg->countBy();
    $tirePreasureCount = $tirePreasure->countBy();
    $glassCount = $glass->countBy();
    $otherOutsideCount = $otherOutside->countBy();
    $otherInsideCount = $otherInside->countBy();

    $lastStatusSummary = $vehicleChecklist->only([
      ...$lampLabel,
      ...$oilLabel,
      ...$tireLabel,
      ...$velgLabel,
      ...$tirePreasureLabel,
      ...$glassLabel,
      ...$otherOutsideLabel,
      ...$otherInsideLabel
    ])->countBy();

    $totalOk = (int) $lastStatusSummary->get(0);
    $totalBroken = (int) $lastStatusSummary->get(1);
    $totalItem = $totalOk + $totalBroken;

    $okItemPercentage = round(($totalOk / $totalItem) * 100);

    $vehicleChecklistData = [
      'Lampu-Lampu' => [
        'config' => ['icon' => 'fa-solid fa-lightbulb'],
        'items' => $lamp,
        'notes' => $vehicleChecklist->get('lamp_notes'),
        'summary' => [
          'ok' => $lampCount->get(0),
          'broken' => $lampCount->get(1),
          'total' => (int) $lampCount->get(0) + (int) $lampCount->get(1)
        ]
      ],
      'Oil' => [
        'config' => ['icon' => 'fa-solid fa-oil-can'],
        'items' => $oil,
        'notes' => $vehicleChecklist->get('equipment_notes'),
        'summary' => [
          'ok' => $oilCount->get(0),
          'broken' => $oilCount->get(1),
          'total' => (int) $oilCount->get(0) + (int) $oilCount->get(1)
        ]
      ],
      'Ban Luar' => [
        'config' => ['icon' => 'fa-solid fa-circle-dot'],
        'items' => $tire,
        'notes' => $vehicleChecklist->get('tire_notes'),
        'summary' => [
          'ok' => $tireCount->get(0),
          'broken' => $tireCount->get(1),
          'total' => (int) $tireCount->get(0) + (int) $tireCount->get(1)
        ]
      ],
      'Velg' => [
        'config' => ['icon' => 'fa-brands fa-first-order-alt'],
        'items' => $velg,
        'summary' => [
          'ok' => $velgCount->get(0),
          'broken' => $velgCount->get(1),
          'total' => (int) $velgCount->get(0) + (int) $velgCount->get(1)
        ]
      ],
      'Tekanan Ban' => [
        'config' => ['icon' => 'fa-solid fa-gauge'],
        'items' => $tirePreasure,
        'summary' => [
          'ok' => $tirePreasureCount->get(0),
          'broken' => $tirePreasureCount->get(1),
          'total' => (int) $tirePreasureCount->get(0) + (int) $tirePreasureCount->get(1)
        ]
      ],
      'Kaca' => [
        'config' => ['icon' => 'fa-regular fa-window-maximize'],
        'items' => $glass,
        'notes' => $vehicleChecklist->get('glass_notes'),
        'summary' => [
          'ok' => $glassCount->get(0),
          'broken' => $glassCount->get(1),
          'total' => (int) $glassCount->get(0) + (int) $glassCount->get(1)
        ]
      ],
      'Lain-Lain Luar' => [
        'config' => ['icon' => 'fa-solid fa-ellipsis'],
        'items' => $otherOutside,
        'notes' => $vehicleChecklist->get('other_notes'),
        'summary' => [
          'ok' => $otherOutsideCount->get(0),
          'broken' => $otherOutsideCount->get(1),
          'total' => (int) $otherOutsideCount->get(0) + (int) $otherOutsideCount->get(1)
        ]
      ],
      'Lain-Lain Dalam' => [
        'config' => ['icon' => 'fa-solid fa-ellipsis'],
        'items' => $otherInside,
        'notes' => $vehicleChecklist->get('other_notes'),
        'summary' => [
          'ok' => $otherInsideCount->get(0),
          'broken' => $otherInsideCount->get(1),
          'total' => (int) $otherInsideCount->get(0) + (int) $otherInsideCount->get(1)
        ]
      ],
    ];

    return [
      'title' => $vehicle->license_plate . ' Last Status',
      'vehicle' => $vehicle,
      'okItemPercentage' => $okItemPercentage,
      'totalOk' => $totalOk,
      'totalBroken' => $totalBroken,
      'vehicleChecklist' => $vehicleChecklistData,
      'vehicleChecklist__ori' => $vehicleChecklist__ori
    ];
  }

  public function show(Request $request, VehicleChecklist $vehicleChecklist)
  {
    $isAjax = $request->ajax();
    $data = $this->buildShowData($vehicleChecklist);

    if ($isAjax) {
      return response()->json([
        'id' => $data['vehicleChecklist__ori']->id,
        'header' => view('admin.vehicle_checklists._header', $data)->render(),
        'info' => view('admin.vehicle_checklists._info', $data)->render(),
        'categories' => view('admin.vehicle_checklists._categories', $data)->render(),
      ]);
    }

    return view('admin.vehicle_checklists.show', $data);
  }

  public function activities(Request $request, VehicleChecklist $vehicleChecklist)
  {
    $labels = $this->allConditionLabels();

    $draw = (int) $request->input('draw', 1);
    $start = max(0, (int) $request->input('start', 0));
    $length = (int) $request->input('length', 50);
    $length = $length > 0 ? min($length, 200) : 50;
    $searchValue = trim((string) $request->input('search.value', ''));
    $orderColumn = (int) $request->input('order.0.column', 5);
    $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

    $baseQuery = VehicleChecklist::where('vehicle_id', $vehicleChecklist->vehicle_id);
    $recordsTotal = (clone $baseQuery)->count();

    $query = (clone $baseQuery)->with(['address', 'user.person']);

    if ($searchValue !== '') {
      $query->where(function ($q) use ($searchValue) {
        $q->where('odo', 'like', "%{$searchValue}%")
          ->orWhereHas('address', fn($a) => $a->where('name', 'like', "%{$searchValue}%"))
          ->orWhereHas('user.person', fn($p) => $p->where('name', 'like', "%{$searchValue}%"));
      });
    }

    $recordsFiltered = (clone $query)->count();

    switch ($orderColumn) {
      case 1: // ODO
        $query->orderBy('odo', $orderDir);
        break;
      case 2: // Location
        $query->join('addresses', 'addresses.id', '=', 'vehicle_checklists.address_id')
          ->orderBy('addresses.name', $orderDir)
          ->select('vehicle_checklists.*');
        break;
      case 4: // Created By
        $query->join('users', 'users.id', '=', 'vehicle_checklists.user_id')
          ->join('people', 'people.id', '=', 'users.person_id')
          ->orderBy('people.name', $orderDir)
          ->select('vehicle_checklists.*');
        break;
      default: // Created At
        $query->orderBy('created_at', $orderDir)->orderBy('id', $orderDir);
        break;
    }

    $items = $query->skip($start)->take($length)->get();

    $rows = $items->map(function (VehicleChecklist $item) use ($labels) {
      $link = route('admin.vehicles-checklists.show', $item->id);

      return [
        '<a href="' . e($link) . '" class="btn btn-primary checklist-link"><i class="fa-solid fa-eye"></i></a>',
        $item->odo,
        e($item->address->name ?? ''),
        $this->conditionPercentage($item, $labels) . '%',
        e($item->user->person->name ?? ''),
        $item->created_at->format('Y-m-d'),
        $item->id,
      ];
    });

    $response = [
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $rows,
    ];

    $findId = $request->input('find_id');
    if ($findId) {
      $response['rank'] = (clone $baseQuery)
        ->where('created_at', '>=', VehicleChecklist::find($findId)?->created_at ?? now())
        ->count() - 1;
    }

    return response()->json($response);
  }

  public function downloadPdf(VehicleChecklist $vehicleChecklist)
  {
    $data = $this->buildShowData($vehicleChecklist);

    $pdf = Pdf::loadView('pdf.vehicle_checklist', $data);

    return $pdf->download('Vehicle-Checklist-' . $data['vehicle']->license_plate . '-' . $vehicleChecklist->id . '.pdf');
  }
}
