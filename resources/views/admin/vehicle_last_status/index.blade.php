@extends('admin.layouts.main')

@section('container')
  <div class="page-content">

    <!-- Page Header-->
    <div class="bg-dash-dark-2 py-4">
      <div class="container-fluid">
        <h2 class="h5 mb-0">Vehicles Last Status</h2>
      </div>
    </div>
    <section class="container-fluid">
      <div class="table-responsive">
        <table class="table table-striped table-dark text-center" data-display="datatables">
          <thead>
            <tr>
              <th>ID</th>
              <th></th>
              <th></th>
              <th>License Plate</th>
              <th>Odo</th>
              <th>Odo Maintenance</th>
              <th>Due Maintenance</th>
              <th>Lamp</th>
              <th>Glass</th>
              <th>Tire</th>
              <th>Equipment</th>
              <th>Gear</th>
              <th>Other</th>
              <th>Latest Condition</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($vehicleLastStatuses as $vehicleLS)
              <tr>
                <td>{{ $vehicleLS->id }}</td>
                <td></td>
                <td>
                  <div class="dropdown">
                    <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                      @if (!empty($vehicleLS->latest_checklist_id))
                        <li>
                          <a href="{{ route('admin.vehicles-checklists.show', $vehicleLS->latest_checklist_id) }}"
                            class="dropdown-item">
                            Last Status
                          </a>
                        </li>
                      @endif
                    </ul>
                  </div>
                </td>
                <td>
                  @php
                    $licese_plate_color = intval($vehicleLS->vehicle_license_plate_color_id) === 2 ? 'bg-warning' : 'bg-white';
                  @endphp
                  <span class="text-dark {{ $licese_plate_color }}">{{ $vehicleLS->license_plate }}</span>
                </td>
                <td>{{ $vehicleLS->odo }}</td>
                <td
                  class={{ decideTextColorByTwoNumber($vehicleLS->odo, $vehicleLS->maintenance_odo, 'text-red-700', 'text-green-300') }}>
                  {{ $vehicleLS->maintenance_odo ?? 'No Data' }}
                </td>
                <td
                  class={{ decideTextColorByDay($vehicleLS->maintenance_date, [0 => 'text-red-700', 15 => 'text-red-300'], 'text-green-300') }}>
                  {{ $vehicleLS->maintenance_date ?? 'No Data' }}
                </td>
                {{-- -------------Checklist--------- --}}
                @php
                  $errorClass = 'text-red-600';
                @endphp
                <td @class([$errorClass => $vehicleLS->total_broken_lamp > 0])>
                  {{ $vehicleLS->total_broken_lamp }}
                </td>
                <td @class([$errorClass => $vehicleLS->total_broken_glass > 0])>
                  {{ $vehicleLS->total_broken_glass }}
                </td>
                <td @class([$errorClass => $vehicleLS->total_broken_tire > 0])>
                  {{ $vehicleLS->total_broken_tire }}
                </td>
                <td @class([$errorClass => $vehicleLS->total_broken_equipment > 0])>
                  {{ $vehicleLS->total_broken_equipment }}
                </td>
                <td @class([$errorClass => $vehicleLS->total_broken_gear > 0])>
                  {{ $vehicleLS->total_broken_gear }}
                </td>
                <td @class([$errorClass => $vehicleLS->total_broken_other > 0])>
                  {{ $vehicleLS->total_broken_other }}
                </td>
                <td>
                  @if (!is_null($vehicleLS->latest_condition_percentage))
                    <span
                      class="{{ $vehicleLS->latest_condition_percentage == 100 ? 'text-green-300' : 'text-red-700' }}">
                      {{ $vehicleLS->latest_condition_percentage }}%
                    </span>
                  @else
                    No Data
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection
