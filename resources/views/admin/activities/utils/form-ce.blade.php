<div class="mb-5">
  <h4 class="text-primary fw-bold">Data Aktivitas</h4>
  <hr>
  <div class="row g-2 mb-2">

    <div class="col-xl-4">
      <label for="do_number" class="form-label">Nomor DO</label>
      <input type="text" class="form-control form-control-lg @error('do_number') is-invalid @enderror" id="do_number"
        name="do_number" value="{{ old('do_number', $activity['do_number']) }}">

      @error('do_number')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="vehicle_id" class="form-label">Kendaraan</label>
      <select class='form-select form-select-lg @error('vehicle_id') is-invalid @enderror' name='vehicle_id'>
        <option value='' hidden></option>
        @foreach ($vehicles as $vehicle)
          @if ($vehicle->id == old('vehicle_id', $activity['vehicle_id']))
            <option value="{{ $vehicle->id }}" selected>{{ $vehicle->license_plate }}</option>
          @else
            <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }}</option>
          @endif
        @endforeach
      </select>

      @error('vehicle_id')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="user_id" class="form-label">User</label>
      <select class='form-select form-select-lg @error('user_id') is-invalid @enderror' name='user_id'>
        <option value='' hidden></option>
        @foreach ($users as $user)
          @if ($user->id == old('user_id', $activity['user_id']))
            <option value="{{ $user->id }}" selected>{{ $user->username }}</option>
          @else
            <option value="{{ $user->id }}">{{ $user->username }}</option>
          @endif
        @endforeach
      </select>

      @error('user_id')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="departure_odo" class="form-label">Odometer Awal</label>
      <input type="number" class="form-control form-control-lg @error('departure_odo') is-invalid @enderror"
        id="departure_odo" name="departure_odo" value="{{ old('departure_odo', $activity['departure_odo']) }}">

      @error('departure_odo')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="departure_location_id" class="form-label">Lokasi Keberangkatan</label>
      <select class='form-select form-select-lg @error('departure_location_id') is-invalid @enderror'
        name='departure_location_id'>
        <option value='' hidden></option>
        @foreach ($addresses as $address)
          @if ($address->id == old('departure_location_id', $activity['departure_location_id']))
            <option value="{{ $address->id }}" selected>{{ $address->name }}</option>
          @else
            <option value="{{ $address->id }}">{{ $address->name }}</option>
          @endif
        @endforeach
      </select>

      @error('departure_location_id')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>


    <div class="col-xl-4">
      <label for="arrival_location_id" class="form-label">Lokasi Kedatangan</label>
      <select class='form-select form-select-lg @error('arrival_location_id') is-invalid @enderror'
        name='arrival_location_id'>
        <option value='' hidden></option>
        @foreach ($addresses as $address)
          @if ($address->id == old('arrival_location_id', $activity['arrival_location_id']))
            <option value="{{ $address->id }}" selected>{{ $address->name }}</option>
          @else
            <option value="{{ $address->id }}">{{ $address->name }}</option>
          @endif
        @endforeach
      </select>

      @error('arrival_location_id')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="project_id" class="form-label">Project</label>
      <select class='form-select form-select-lg @error('project_id') is-invalid @enderror' name='project_id'>
        <option value='' hidden></option>
        @foreach ($projects as $project)
          @if ($project->id == old('project_id', $activity['project_id']))
            <option value="{{ $project->id }}" selected>{{ $project->name }}</option>
          @else
            <option value="{{ $project->id }}">{{ $project->name }}</option>
          @endif
        @endforeach
      </select>

      @error('project_id')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>


    <div class="col-xl-4">
      <label for="departure_date" class="form-label">Waktu Keberangkatan</label>
      <input type="datetime-local" class="form-control form-control-lg @error('departure_date') is-invalid @enderror"
        id="departure_date" name="departure_date"
        value="{{ old(
            'departure_date',
            $activity['departure_date'] ? date('Y-m-d\TH:i:s', strtotime($activity['departure_date'])) : null,
        ) }}"
        step="1">

      @error('departure_date')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>


    <div class="col-xl-4">
      <label for="arrival_date" class="form-label">Waktu Kedatangan</label>
      <input type="datetime-local" class="form-control form-control-lg @error('arrival_date') is-invalid @enderror"
        id="arrival_date" name="arrival_date"
        value="{{ old(
            'arrival_date',
            $activity['arrival_date'] ? date('Y-m-d\TH:i:s', strtotime($activity['arrival_date'])) : null,
        ) }}"
        step="1">

      @error('arrival_date')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="do_date" class="form-label">Waktu DO</label>
      <input type="date" class="form-control form-control-lg @error('do_date') is-invalid @enderror" id="do_date"
        name="do_date" value="{{ old('do_date', $activity['do_date']) }}">

      @error('do_date')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="arrival_odo" class="form-label">ODO Kedatangan</label>
      <input type="number" class="form-control form-control-lg @error('arrival_odo') is-invalid @enderror"
        id="arrival_odo" name="arrival_odo" value="{{ old('arrival_odo', $activity['arrival_odo']) }}">

      @error('arrival_odo')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="type" class="form-label">Tipe</label>
      <input class="form-control form-control-lg" name="type" list="typeListOptions"
        value="{{ old('type', $activity['type']) }}" id="type">
      <datalist id="typeListOptions">
        <option value="SDP">
        <option value="MDP-1">
        <option value="MDP-2">
        <option value="MDP-3">
        <option value="MDP-4">
        <option value="MDP-5">
        <option value="MDP-E">
        <option value="RETURN">
        <option value="MANUVER">
        <option value="MAINTENANCE">
        <option value="KIR">
      </datalist>

      @error('type')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="status" class="form-label">Status</label>
      <input class="form-control form-control-lg" name="status" list="statuslistOptions"
        value="{{ old('status', $activity->activityStatus->status ?? null) }}" id="status">
      <datalist id="statuslistOptions">
        <option value="draft">
        <option value="pending">
        <option value="approved">
        <option value="paid">
        <option value="rejected">
      </datalist>

      @error('status')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="bbm_amount" class="form-label">BBM</label>
      <input type="text" class="form-control form-control-lg @error('bbm_amount') is-invalid @enderror"
        id="bbm_amount" name="bbm_amount"
        value="{{ old('bbm_amount', $activity->activityStatus->activityPayment->bbm_amount ?? null) }}"
        data="money">

      @error('bbm_amount')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="toll_amount" class="form-label">Toll</label>
      <input type="text" class="form-control form-control-lg @error('toll_amount') is-invalid @enderror"
        id="toll_amount" name="toll_amount"
        value="{{ old('toll_amount', $activity->activityStatus->activityPayment->toll_amount ?? null) }}"
        data="money">

      @error('toll_amount')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="maintenance_amount" class="form-label">Maintenance</label>
      <input type="text" class="form-control form-control-lg @error('maintenance_amount') is-invalid @enderror"
        id="maintenance_amount" name="maintenance_amount"
        value="{{ old('maintenance_amount', $activity->activityStatus->activityPayment->maintenance_amount ?? null) }}"
        data="money">

      @error('maintenance_amount')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="load_amount" class="form-label">Load</label>
      <input type="text" class="form-control form-control-lg @error('load_amount') is-invalid @enderror"
        id="load_amount" name="load_amount"
        value="{{ old('load_amount', $activity->activityStatus->activityPayment->load_amount ?? null) }}"
        data="money">

      @error('load_amount')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="unload_amount" class="form-label">Unload</label>
      <input type="text" class="form-control form-control-lg @error('unload_amount') is-invalid @enderror"
        id="unload_amount" name="unload_amount"
        value="{{ old('unload_amount', $activity->activityStatus->activityPayment->unload_amount ?? null) }}"
        data="money">

      @error('unload_amount')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="col-xl-4">
      <label for="parking_amount" class="form-label">Parkir</label>
      <input type="text" class="form-control form-control-lg @error('parking_amount') is-invalid @enderror"
        id="parking_amount" name="parking_amount"
        value="{{ old('parking_amount', $activity->activityStatus->activityPayment->parking_amount ?? null) }}"
        data="money">

      @error('parking_amount')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>

  </div>
</div>
<div class="mb-5">
  <h4 class="text-primary fw-bold">Gambar</h4>
  <hr>
  <div class="row g-2 mb-2">

    <div class="col-xl-4">
      <label for="do_image" class="form-label">Gambar DO</label>
      <input type="file" accept="image/*"
        class="form-control form-control-lg mb-3 @error('do_image') is-invalid @enderror" id="do_image"
        name="do_image" onchange="previewImage('do_image')">

      @error('do_image')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
      <img src="{{ asset('storage/' . $activity['do_image']) }}"
        class="img-fluid rounded zoom mw-100 mx-auto d-block" style="max-height: 200px" id="do_image-preview"
        alt="" data-action="zoom">
    </div>

    <div class="col-xl-4">
      <label for="departure_odo_image" class="form-label">ODO Keberangkatan</label>
      <input type="file" accept="image/*"
        class="form-control form-control-lg mb-3 @error('departure_odo_image') is-invalid @enderror"
        id="departure_odo_image" name="departure_odo_image" onchange="previewImage('departure_odo_image')">

      @error('departure_odo_image')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
      <img src="{{ asset('storage/' . $activity['departure_odo_image']) }}"
        class="img-fluid rounded zoom mw-100 mx-auto d-block" style="max-height: 200px"
        id="departure_odo_image-preview" alt="" data-action="zoom">
    </div>

    <div class="col-xl-4">
      <label for="arrival_odo_image" class="form-label">ODO Kedatangan</label>
      <input type="file" accept="image/*"
        class="form-control form-control-lg mb-3 @error('arrival_odo_image') is-invalid @enderror"
        id="arrival_odo_image" name="arrival_odo_image" onchange="previewImage('arrival_odo_image')">

      @error('arrival_odo_image')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
      <img src="{{ asset('storage/' . $activity['arrival_odo_image']) }}"
        class="img-fluid rounded zoom mw-100 mx-auto d-block" style="max-height: 200px"
        id="arrival_odo_image-preview" alt="" data-action="zoom">
    </div>

    <div class="col-xl-4">
      <label for="bbm_image" class="form-label">BBM</label>
      <input type="file" accept="image/*"
        class="form-control form-control-lg mb-3 @error('bbm_image') is-invalid @enderror" id="bbm_image"
        name="bbm_image" onchange="previewImage('bbm_image')">

      @error('bbm_image')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
      <img src="{{ asset('storage/' . $activity['bbm_image']) }}"
        class="img-fluid rounded zoom mw-100 mx-auto d-block" style="max-height: 200px" id="bbm_image-preview"
        alt="" data-action="zoom">
    </div>

    <div class="col-xl-4">
      <label for="toll_image" class="form-label">Toll</label>
      <input type="file" accept="image/*"
        class="form-control form-control-lg mb-3 @error('toll_image') is-invalid @enderror" id="toll_image"
        name="toll_image" onchange="previewImage('toll_image')">

      @error('toll_image')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
      <img src="{{ asset('storage/' . $activity['toll_image']) }}"
        class="img-fluid rounded zoom mw-100 mx-auto d-block" style="max-height: 200px" id="toll_image-preview"
        alt="" data-action="zoom">
    </div>

    <div class="col-xl-4">
      <label for="parking_image" class="form-label">Parkir</label>
      <input type="file" accept="image/*"
        class="form-control form-control-lg mb-3 @error('parking_image') is-invalid @enderror" id="parking_image"
        name="parking_image" onchange="previewImage('parking_image')">

      @error('parking_image')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
      <img src="{{ asset('storage/' . $activity['parking_image']) }}"
        class="img-fluid rounded zoom mw-100 mx-auto d-block" style="max-height: 200px" id="parking_image-preview"
        alt="" data-action="zoom">
    </div>
  </div>
</div>
