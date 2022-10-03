@extends('admin.layouts.index')

@section('container')
  <div class="page-content">
    <div class="bg-dash-dark-2 py-4">
      <div class="container-fluid">
        <h2 class="h5 mb-0">Areas</h2>
      </div>
    </div>

    <section class="container-fluid">
      <div class="table-responsive">
        <table class="table table-striped table-dark text-center" data-display="datatables">
          <thead>
            <tr class="header">
              <th>ID</th>
              <th></th>
              <th></th>
              <th>Name</th>
              <th>Regional</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($areas as $area)
              <tr>
                <td>{{ $area->id }}</td>
                <td></td>
                <td class="d-flex gap-2">
                  {{-- @canany('area-edit') --}}
                  <a href="{{ route('admin.areas.edit', $area->id) }}" class="btn btn-primary">
                    Edit
                  </a>

                  {{-- <form action="{{ route('admin.areas.destroy', $area->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                  </form> --}}
                  {{-- @endcan --}}
                </td>
                <td>{{ $area->name }}</td>
                <td>{{ $area->regional->name }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection
