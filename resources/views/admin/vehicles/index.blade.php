@extends('admin.layouts.main')

@section('container')
  <div class="page-content">

    <!-- Page Header-->
    <div class="bg-dash-dark-2 py-4">
      <div class="container-fluid">
        <h2 class="h5 mb-0">Vehicles</h2>
      </div>
    </div>
    <section class="container-fluid">
      @include('admin.partials.import')
      <h4 class="text-primary fw-bold">Action</h4>
      <hr>
      <input type="hidden" id="tableName" value="vehicles">
      <div class="d-flex mb-5" id="actionContainer"></div>

      <h4 class="text-primary fw-bold">Table</h4>
      <hr>
      <div class="table-responsive">
        <table class="table table-striped table-dark text-center" data-display="datatables"
          data-ajax="{{ route('admin.vehicles.datatable') }}">
          <thead>
            <tr>
              <th>ID</th>
              <th></th>
              <th></th>
              <th>License Plate</th>
              <th>Owner</th>
              <th>Project</th>
              <th>Brand</th>
              <th>Model</th>
              <th>Odo</th>
              <th>KIR Exp</th>
              <th>STNK Exp</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>
  </div>
@endsection
