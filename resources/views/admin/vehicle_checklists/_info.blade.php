<div class="row gap-3 h-100 d-flex flex-column">
  <div class="col-12">
    <div class="card rounded">
      <div class="card-body">
        <div class="row gy-3">
          <div class="col-3">
            <i class="bi bi-kanban display-5 text-gray-700"></i>
          </div>
          <div class="col-9 text-end">
            <p class="text-gray-600 text-uppercase fs-6 mb-0">License Plate</p>
            <p class="fs-4 mb-0 text-gray-500 text-truncate"> {{ $vehicle->license_plate }} </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card rounded">
      <div class="card-body">
        <div class="row gy-3">
          <div class="col-3">
            <i class="bi bi-speedometer2 display-5 text-gray-700"></i>
          </div>
          <div class="col-9 text-end">
            <p class="text-gray-600 text-uppercase fs-6 mb-0">Odo</p>
            <p class="fs-4 mb-0 text-gray-500 text-truncate">{{ $vehicleChecklist__ori->odo }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card rounded">
      <div class="card-body">
        <div class="row gy-3">
          <div class="col-3">
            <i class="bi bi-person display-5 text-gray-700"></i>
          </div>
          <div class="col-9 text-end">
            <p class="text-gray-600 text-uppercase fs-6 mb-0">Driver</p>
            <p class="fs-4 mb-0 text-gray-500 text-truncate">{{ $vehicleChecklist__ori->user->person->name ?? '-' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card rounded">
      <div class="card-body">
        <div class="row gy-3">
          <div class="col-3">
            <i class="bi bi-geo-alt display-5 text-gray-700"></i>
          </div>
          <div class="col-9 text-end">
            <p class="text-gray-600 text-uppercase fs-6 mb-0">Lokasi</p>
            <p class="fs-4 mb-0 text-gray-500 text-truncate">{{ $vehicle->address->name }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 flex-grow-1 d-flex">
    <div class="card rounded h-100 w-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <p class="fs-4 text-gray-600">Vehicle condition is</p>
            <p class="text-{{ $okItemPercentage == 100 ? 'green' : 'red' }}-700 fs-3 fw-bold mb-0">
              {{ $okItemPercentage == 100 ? 'Good' : 'Critical' }}
            </p>
          </div>
          <div class="display-4 fw-bold text-light">
            {{ $okItemPercentage }}%
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
