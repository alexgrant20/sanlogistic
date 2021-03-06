@extends('admin.layouts.index')

@section('container')
  <div class="page-content">
    <!-- Page Header-->
    <div class="bg-dash-dark-2 py-4">
      <div class="container-fluid">
        <h2 class="h5 mb-0">Activities</h2>
      </div>
    </div>
    <section class="container-fluid">
      <div class="row mb-4 g-3">
        <x-summary-box>
          <x-slot name="summaryTitle">Total Activity</x-slot>
          <x-slot name="summaryTotal">{{ $activities->count() }}</x-slot>
          <x-slot name="icon">bi bi-journal-text</x-slot>
          <x-slot name="id">total-activity</x-slot>
          <x-slot name="summaryTotalColor">text-dash-color-1</x-slot>
          <x-slot name="customCardClass">disabled</x-slot>
        </x-summary-box>
        <x-summary-box>
          <x-slot name="summaryTitle">On Going</x-slot>
          <x-slot name="summaryTotal">
            {{ $activities->filter(fn($item) => $item->status === 'draft')->count() }}
          </x-slot>
          <x-slot name="icon">bi bi-journal-arrow-up</x-slot>
          <x-slot name="id">total-ongoing-activity</x-slot>
          <x-slot name="summaryTotalColor">text-info</x-slot>
          <x-slot name="customCardClass">disabled</x-slot>
        </x-summary-box>
        <x-summary-box>
          <x-slot name="summaryTitle">Pending</x-slot>
          <x-slot name="summaryTotal">
            {{ $activities->filter(fn($item) => $item->status === 'pending')->count() }}
          </x-slot>
          <x-slot name="icon">bi bi-journal-medical</x-slot>
          <x-slot name="id">total-pending-activity</x-slot>
          <x-slot name="summaryTotalColor">text-warning</x-slot>
          <x-slot name="customCardClass">disabled</x-slot>
        </x-summary-box>
        <x-summary-box>
          <x-slot name="summaryTitle">Approved</x-slot>
          <x-slot name="summaryTotal">
            {{ $activities->filter(fn($item) => $item->status === 'approved')->count() }}
          </x-slot>
          <x-slot name="icon">bi bi-journal-check</x-slot>
          <x-slot name="id">total-approved-activity</x-slot>
          <x-slot name="summaryTotalColor">text-success</x-slot>
          <x-slot name="customCardClass">disabled</x-slot>
        </x-summary-box>
        <x-summary-box>
          <x-slot name="summaryTitle">Rejected</x-slot>
          <x-slot name="summaryTotal">
            {{ $activities->filter(fn($item) => $item->status === 'rejected')->count() }}
          </x-slot>
          <x-slot name="icon">bi bi-journal-x</x-slot>
          <x-slot name="id">total-rejected-activity</x-slot>
          <x-slot name="summaryTotalColor">text-primary</x-slot>
          <x-slot name="customCardClass">disabled</x-slot>
        </x-summary-box>
        <x-summary-box>
          <x-slot name="summaryTitle">Paid</x-slot>
          <x-slot name="summaryTotal">
            {{ $activities->filter(fn($item) => $item->status === 'paid')->count() }}
          </x-slot>
          <x-slot name="icon">bi bi-wallet-fill</x-slot>
          <x-slot name="id">total-paid-activity</x-slot>
          <x-slot name="summaryTotalColor">text-dash-color-2</x-slot>
          <x-slot name="customCardClass">disabled</x-slot>
        </x-summary-box>
      </div>

      @include('admin.partials.import')

      @if (session()->has('log_data'))
        <x-modal id="my-modal">
          <x-slot name="title">Activity Log</x-slot>
          <x-slot name="class">openModal</x-slot>
          <x-slot name="size">modal-lg</x-slot>
          <x-slot name="body">
            <table class="table table-hover table-dark text-center nowrap" style="widths: 100%">
              <tr>
                <th>Status</th>
                <th>By</th>
                <th>Time</th>
              </tr>
              @foreach (session('log_data') as $log)
                <tr>
                  <td>{{ $log->status }}</td>
                  <td>{{ $log->name }}</td>
                  <td>{{ $log->created_at }}</td>
                </tr>
              @endforeach
            </table>
          </x-slot>
          <x-slot name="footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </x-slot>
        </x-modal>
      @endif

      <h4 class="text-primary fw-bold">Action</h4>
      <hr>
      <input type="hidden" id="tableName" value="activities">
      <div class="d-flex mb-5" id="actionContainer"></div>

      <h4 class="text-primary fw-bold">Table</h4>
      <hr>
      <table class="table table-hover text-center table-dark nowrap" style="width: 100%" data-display="datatables">
        <thead>
          <tr class="header">
            <th>ID</th>
            <th>Action</th>
            <th>Tanggal</th>
            <th>Nama Pengendara</th>
            <th>Nomor Kendaraan</th>
            <th>Nomor DO</th>
            <th>Lokasi Keberangkatan</th>
            <th>Lokasi Tujuan</th>
            <th>Jenis Aktifitas</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($activities as $activity)
            <tr>
              <td>{{ $activity->id }}</td>
              <td>
                <a href="{{ route('admin.activity.edit', $activity->id) }}" class="badge bg-primary fs-6">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="{{ route('admin.activity.log', $activity->id) }}" class="badge bg-info fs-6">
                  <i class="bi bi-journal-text"></i>
                </a>
              </td>
              <td>{{ $activity->departure_date }}</td>
              <td>{{ $activity->person_name }}</td>
              <td>{{ $activity->license_plate }}</td>
              <td>{{ $activity->do_number }}</td>
              <td>{{ $activity->departure_name }}</td>
              <td>{{ $activity->arrival_name }}</td>
              <td>{{ $activity->type }}</td>
              <td>{{ $activity->status }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </section>
  </div>
@endsection
