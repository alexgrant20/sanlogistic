@extends('admin.layouts.main')

@section('container')
   <div class="page-content">
      <div class="bg-dash-dark-2 py-4">
         <div class="container-fluid">
            <h2 class="h5 mb-0">Driver Finance</h2>
         </div>
      </div>
      <section class="container-fluid">
         <h4 class="text-primary fw-bold">Table</h4>
         <hr>
         <div class="table-responsive" style="max-width: 100%; overflow:hidden;">
            <table class="table table-striped table-dark text-center" id="activities">
               <thead>
                  <tr class="header">
                     <th>Username</th>
                     <th>Total Payment</th>
                  </tr>
               </thead>
               <tbody>
               </tbody>
            </table>
         </div>
      </section>
   </div>
@endsection

@section('headJS')
   <script>
      document.addEventListener("DOMContentLoaded", function() {
         $('#activities').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: false,
            paging: true,
            ajax: "{{ route('admin.people.finance.list') }}",
            deferRender: true,
            columns: [{
               data: 'name',
               name: 'name'
            },
            {
               data: 'total_payment',
               name: 'total_payment',
               render: $.fn.dataTable.render.number(',', '.', 2, 'Rp ')
            }],
         });
      });
   </script>
@endsection
