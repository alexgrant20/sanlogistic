@php
function checkChecklist($checklist)
{
    if (gettype($checklist) !== 'integer') {
        return ['text-warning', 'Error'];
    }
    $label = $checklist == 1 ? 'fa-x' : 'fa-check';
    $textColor = $checklist == 1 ? 'text-red-400' : 'text-green-400';

    return [$textColor, $label];
}

@endphp

@foreach ($vehicleChecklist as $key => $checklistConf)
  @php
    $summary = $checklistConf['summary'];
    $config = $checklistConf['config'];
  @endphp
  <div class="col-sm-12 col-md-6 col-xl-6 col-xxl-4" @class([
      'order-1' => $summary['broken'],
  ])>
    <div class="card rounded h-100">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <i class="{{ $config['icon'] }} fs-1 mb-3"></i>
            <h3 class="fs-3">{{ $key }}</h3>
          </div>
        </div>
        <div class="progress">
          <div class="progress-bar bg-success fs-6" role="progressbar"
            style="width: {{ getPercentage($summary['ok'], $summary['total']) }}%"
            aria-valuenow="{{ getPercentage($summary['ok'], $summary['total']) }}" aria-valuemin="0"
            aria-valuemax="100">
            @if ($summary['ok'] != 0)
              {{ getPercentage($summary['ok'], $summary['total']) }}%
            @endif
          </div>
        </div>
      </div>
      <div class="card-body d-flex justify-content-between flex-column">
        <div class="row g-3">
          @foreach ($checklistConf['items'] as $key => $item)
            @php
              $checklistVal = checkChecklist($item);
              $order = $item == 1 ? '1' : '2';
            @endphp
            <div class="col-12 d-flex justify-content-between order-{{ $order }}">
              <span class="text-red-100">{{ ucwords(Str::replace('_', ' ', $key)) }}</span>
              <span class="{{ $checklistVal[0] }}">
                <i class="fa-solid {{ $checklistVal[1] }} fs-4"></i>
              </span>
            </div>
          @endforeach
        </div>

        @if (!empty($checklistConf['notes']))
          <div class="mt-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control" rows="3">{{ $checklistConf['notes'] }}</textarea>
          </div>
        @endif
      </div>
    </div>
  </div>
@endforeach
@if ($vehicleChecklist__ori->vehicleChecklistImage->isNotEmpty())
  <div class="col-md-4">
    <div class="card rounded">
      <div class="card-header">
        Other Desc
      </div>
      <div class="card-body">
        <div id="carousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-indicators">
            @foreach ($vehicleChecklist__ori->vehicleChecklistImage as $checklistImage)
              <button type="button" data-bs-target="#carousel" data-bs-slide-to="{{ $loop->index }}"
                @class(['active' => $loop->iteration == 1]) aria-current="{{ $loop->iteration == 1 }}"
                aria-label="Slide {{ $loop->iteration }}"></button>
            @endforeach
          </div>
          <div class="carousel-inner">
            @foreach ($vehicleChecklist__ori->vehicleChecklistImage as $checklistImage)
              <div @class(['carousel-item', 'active' => $loop->iteration == 1])>
                <div style="width: auto; height: 300px; max-height: 300px">
                  <img src="{{ asset("storage/$checklistImage->image") }}"
                    class="d-block m-auto w-100 h-100 checklist-zoomable-image" style="cursor: zoom-in"
                    data-bs-toggle="modal" data-bs-target="#imageZoomModal"
                    data-full-src="{{ asset("storage/$checklistImage->image") }}" alt="">
                </div>
                @if ($checklistImage->description)
                  <div class="carousel-caption d-none d-md-block" style="background: rgba(0, 0, 0, 1)">
                    <p class="d-inline">{{ $checklistImage->description }}</p>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carousel"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carousel"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </div>
  </div>
@endif
