@extends('admin.layouts.main')

@section('container')
  <div class="page-content">
    <div class="bg-dash-dark-2 py-4">
      <div class="container-fluid">
        <h2 class="h5 mb-0">Audit Activity</h2>
      </div>
    </div>

    <section class="container-fluid pb-0">
      <div class="mb-5">
        <h4 class="text-primary fw-bold">Detail Aktivitas</h4>
        <hr>
        <div class="row g-2 mb-5">
          <div class="col-xl-4">
            <label for="do_number" class="form-label fs-4 fw-bold">Nama Pengendara</label>
            <p class="fs-4" id="do_number">{{ $activity->driver->person->name }}</p>
          </div>
          <div class="col-xl-4">
            <label for="do_number" class="form-label fs-4 fw-bold">DO Number</label>
            <p class="fs-4" id="do_number">{{ $activity->do_number }}</p>
          </div>
        </div>
      </div>

      <form action="{{ route('admin.finances.audit', $activity->id) }}" method="post" id="form">
        @csrf
        @method('PUT')
        <div class="mb-5">
          <h4 class="text-primary fw-bold">Pengeluaran</h4>
          <hr>
          <div class="row g-2 mb-5">
            <div class="col-xl-4">
              <label for="bbm_amount" class="form-label">BBM</label>
              <input type="text" class="form-control form-control-lg @error('bbm_amount') is-invalid @enderror"
                id="bbm_amount" name="bbm_amount"
                value="{{ old('bbm_amount', $activity->activityStatus->activityPayment->bbm_amount) }}" data="money">

              @error('bbm_amount')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror

              <img src="{{ asset('storage/' . $activity['bbm_image']) }}"
                class="img-fluid rounded zoom mw-100 mx-auto d-block mt-5" style="max-height: 200px"
                id="bbm_image-preview" alt="" data-action="zoom">
            </div>

            <div class="col-xl-4">
              <label for="toll_amount" class="form-label">Toll</label>
              <input type="text" class="form-control form-control-lg @error('toll_amount') is-invalid @enderror"
                id="toll_amount" name="toll_amount"
                value="{{ old('toll_amount', $activity->activityStatus->activityPayment->toll_amount) }}" data="money">

              @error('toll_amount')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror

              <img src="{{ asset('storage/' . $activity['toll_image']) }}"
                class="img-fluid rounded zoom mw-100 mx-auto d-block mt-5" style="max-height: 200px"
                id="toll_image-preview" alt="" data-action="zoom">
            </div>

            <div class="col-xl-4">
              <label for="maintenance_amount" class="form-label">Maintenance</label>
              <input type="text" class="form-control form-control-lg @error('maintenance_amount') is-invalid @enderror"
                id="maintenance_amount" name="maintenance_amount"
                value="{{ old('maintenance_amount', $activity->activityStatus->activityPayment->maintenance_amount) }}"
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
                value="{{ old('load_amount', $activity->activityStatus->activityPayment->load_amount) }}" data="money">

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
                value="{{ old('unload_amount', $activity->activityStatus->activityPayment->unload_amount) }}"
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
                value="{{ old('parking_amount', $activity->activityStatus->activityPayment->parking_amount) }}"
                data="money">

              @error('parking_amount')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror

              <img src="{{ asset('storage/' . $activity['parking_image']) }}"
                class="img-fluid rounded zoom mw-100 mx-auto d-block mt-5" style="max-height: 200px"
                id="parking_image-preview" alt="" data-action="zoom">
            </div>
          </div>
      </form>
    </section>
    <div class="container-fluid">
      <button class="btn btn-lg btn-primary" id="submit">Audit Activity</button>
    </div>
  </div>
@endsection

@section('footJS')
  {!! JsValidator::formRequest('App\Http\Requests\Admin\UpdateActivityCostRequest', 'form') !!}
@endsection
