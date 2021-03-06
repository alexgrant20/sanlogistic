@extends('admin.layouts.main')

@section('container')
  <div class="page-content">
    <div class="bg-dash-dark-2 py-4">
      <div class="container-fluid">
        <h2 class="h5 mb-0">Update Person</h2>
      </div>
    </div>
    <section class="container-fluid">
      <form action="{{ route('admin.person.update', $person->id) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @csrf

        @include('admin.people.utils.form-ce')

        <button type="submit" class="btn btn-lg btn-primary">Update</button>
      </form>
    </section>
  </div>
@endsection
