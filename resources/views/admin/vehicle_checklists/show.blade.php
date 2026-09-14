@extends('admin.layouts.main')

@section('headCSS')
  <style>
    .skeleton {
      background: linear-gradient(90deg, rgba(255, 255, 255, 0.08) 25%, rgba(255, 255, 255, 0.18) 37%, rgba(255, 255, 255, 0.08) 63%);
      background-size: 400% 100%;
      animation: checklist-skeleton-loading 1.4s ease infinite;
      border-radius: 4px;
    }

    @keyframes checklist-skeleton-loading {
      0% {
        background-position: 100% 50%;
      }

      100% {
        background-position: 0 50%;
      }
    }

    .checklist-flex-fix {
      min-height: 0;
    }

    #activitiesTableWrapper {
      position: relative;
    }

    #activitiesTableWrapper .dataTables_scrollBody {
      overflow-y: auto;
    }
  </style>
  <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/datatable/scroller.dataTables.min.css') }}" />
@endsection

@section('container')
  <div class="page-content">
    <div class="bg-dash-dark-2 py-4" id="checklistHeader">
      @include('admin.vehicle_checklists._header')
    </div>
    <section class="container-fluid">
      <div class="row gy-3 mb-5">
        <div class="col-xxl-4" id="checklistInfo">
          @include('admin.vehicle_checklists._info')
        </div>
        <div class="col-xxl-8">
          @include('admin.vehicle_checklists._table')
        </div>
      </div>

      <div class="row g-4" id="checklistCategories">
        @include('admin.vehicle_checklists._categories')
      </div>
    </section>

    <!-- Image Zoom Modal -->
    <div class="modal fade" id="imageZoomModal" tabindex="-1" aria-labelledby="imageZoomModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark">
          <div class="modal-header border-0">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body d-flex justify-content-center align-items-center p-0">
            <img id="imageZoomModalImg" src="" class="img-fluid" style="max-height: 80vh" alt="">
          </div>
        </div>
      </div>
    </div>
  </div>

  <template id="checklistSkeletonTemplate">
    <div data-skeleton="header" class="d-flex justify-content-between align-items-center">
      <div class="skeleton" style="width: 180px; height: 20px;"></div>
      <div class="skeleton" style="width: 110px; height: 32px; border-radius: 6px;"></div>
    </div>

    <div data-skeleton="info">
      @for ($i = 0; $i < 5; $i++)
        <div class="col-12 mb-3">
          <div class="card rounded">
            <div class="card-body">
              <div class="row gy-3">
                <div class="col-3">
                  <div class="skeleton" style="width: 48px; height: 48px; border-radius: 50%;"></div>
                </div>
                <div class="col-9 text-end">
                  <div class="skeleton ms-auto mb-2" style="width: 60%; height: 12px;"></div>
                  <div class="skeleton ms-auto" style="width: 80%; height: 20px;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endfor
    </div>

    <div data-skeleton="categories">
      @for ($i = 0; $i < 8; $i++)
        <div class="col-sm-12 col-md-6 col-xl-6 col-xxl-4">
          <div class="card rounded h-100">
            <div class="card-header">
              <div class="skeleton mb-3" style="width: 40px; height: 40px; border-radius: 8px;"></div>
              <div class="skeleton mb-3" style="width: 50%; height: 18px;"></div>
              <div class="skeleton" style="width: 100%; height: 8px;"></div>
            </div>
            <div class="card-body">
              @for ($j = 0; $j < 4; $j++)
                <div class="d-flex justify-content-between mb-3">
                  <div class="skeleton" style="width: 40%; height: 14px;"></div>
                  <div class="skeleton" style="width: 20px; height: 14px;"></div>
                </div>
              @endfor
            </div>
          </div>
        </div>
      @endfor
    </div>
  </template>
@endsection

@section('footJS')
  <script type="text/javascript" src="{{ asset('/vendor/datatable/dataTables.scroller.min.js') }}"></script>
  <script>
    const initialChecklistId = {{ $vehicleChecklist__ori->id }};
    const activitiesUrl = {!! json_encode(route('admin.vehicles-checklists.activities', $vehicleChecklist__ori->id)) !!};
    let activitiesTable;
    let currentHighlightId = initialChecklistId;

    function initActivitiesTable() {
      const table = $('#activities').DataTable({
        serverSide: true,
        scroller: true,
        deferRender: true,
        scrollCollapse: true,
        scrollY: '400px',
        dom: 'frti',
        order: [
          [5, 'desc']
        ],
        ajax: {
          url: activitiesUrl,
          type: 'GET'
        },
        columns: [{
            data: 0,
            orderable: false,
            searchable: false
          },
          {
            data: 1
          },
          {
            data: 2,
            className: 'text-truncate',
            createdCell: function(td) {
              $(td).css('max-width', '150px');
            }
          },
          {
            data: 3,
            orderable: false,
            searchable: false
          },
          {
            data: 4,
            className: 'text-truncate',
            createdCell: function(td) {
              $(td).css('max-width', '150px');
            }
          },
          {
            data: 5
          },
        ],
        createdRow: function(row, data) {
          $(row).attr('data-checklist-id', data[6]);
          if (String(data[6]) === String(currentHighlightId)) {
            $(row).addClass('table-primary');
          }
        }
      });

      table.on('init.dt', function() {
        $('#activitiesSkeleton').addClass('d-none');
        scheduleResizeActivitiesTable();
        scrollToChecklist(initialChecklistId, false);
      });

      return table;
    }

    function getScrollerApi() {
      if (!activitiesTable || !activitiesTable.scroller) return null;
      const proxy = activitiesTable.scroller();
      return proxy && typeof proxy.scroller === 'function' ? proxy.scroller : null;
    }

    function resizeActivitiesTable() {
      const $container = $('#activitiesTableWrapper');
      const $dtWrapper = $container.children('.dataTables_wrapper');
      const $scrollBody = $dtWrapper.find('.dataTables_scrollBody');
      if (!$dtWrapper.length || !$scrollBody.length) return;

      const chromeHeight = $dtWrapper.outerHeight(true) - $scrollBody.outerHeight(true);
      const available = Math.max(150, $container.innerHeight() - chromeHeight);
      $scrollBody.css({
        'max-height': available + 'px',
        height: available + 'px'
      });

      const scrollerApi = getScrollerApi();
      if (scrollerApi) {
        scrollerApi.measure(false);
      }
    }

    let resizeActivitiesTableTimeout;

    function scheduleResizeActivitiesTable() {
      clearTimeout(resizeActivitiesTableTimeout);
      resizeActivitiesTableTimeout = setTimeout(resizeActivitiesTable, 50);
    }

    function highlightChecklistRow(id) {
      currentHighlightId = id;

      $('#activities tbody tr').removeClass('table-primary');
      $('#activities tbody tr[data-checklist-id="' + id + '"]').addClass('table-primary');
    }

    function isDefaultTableState() {
      if (!activitiesTable) return false;
      const order = activitiesTable.order();
      const isDefaultOrder = order.length === 1 && order[0][0] === 5 && order[0][1] === 'desc';
      const isUnfiltered = activitiesTable.search() === '';
      return isDefaultOrder && isUnfiltered;
    }

    function scrollToChecklist(id, animate) {
      highlightChecklistRow(id);
      if (!activitiesTable || !isDefaultTableState()) return;

      $.getJSON(activitiesUrl, {
          find_id: id,
          start: 0,
          length: 1,
          draw: 0
        })
        .done(function(resp) {
          const scrollerApi = getScrollerApi();
          if (typeof resp.rank === 'number' && scrollerApi) {
            scrollerApi.toPosition(resp.rank, animate !== false);
          }
        });
    }

    function bindImageZoomModal() {
      const imageZoomModalEl = document.getElementById('imageZoomModal');
      const imageZoomModalImg = document.getElementById('imageZoomModalImg');
      if (imageZoomModalEl) {
        imageZoomModalEl.addEventListener('show.bs.modal', function(event) {
          const trigger = event.relatedTarget;
          imageZoomModalImg.src = trigger.getAttribute('data-full-src');
        });
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      activitiesTable = initActivitiesTable();
      bindImageZoomModal();
      scheduleResizeActivitiesTable();
    });

    window.addEventListener('resize', scheduleResizeActivitiesTable);

    function showChecklistSkeleton() {
      const tpl = document.getElementById('checklistSkeletonTemplate');
      if (!tpl) return;

      const $frag = $(tpl.content.cloneNode(true));
      $('#checklistHeader').html($frag.find('[data-skeleton="header"]').html());
      $('#checklistInfo').html($frag.find('[data-skeleton="info"]').html());
      $('#checklistCategories').html($frag.find('[data-skeleton="categories"]').html());
    }

    function loadVehicleChecklist(url, pushState) {
      showChecklistSkeleton();

      $.getJSON(url)
        .done(function(data) {
          $('#checklistHeader').html(data.header);
          $('#checklistInfo').html(data.info);
          $('#checklistCategories').html(data.categories);
          scrollToChecklist(data.id, true);
          scheduleResizeActivitiesTable();
          if (pushState) {
            history.pushState({
              checklistUrl: url
            }, '', url);
          }
        })
        .fail(function() {
          window.location.href = url;
        });
    }

    $(document).on('click', '#activities a.checklist-link', function(e) {
      e.preventDefault();
      loadVehicleChecklist($(this).attr('href'), true);
    });

    window.addEventListener('popstate', function() {
      loadVehicleChecklist(location.href, false);
    });
  </script>
@endsection
