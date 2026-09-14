<div class="card rounded h-100 d-flex flex-column">
  <div class="card-body d-flex flex-column flex-grow-1 checklist-flex-fix">
    <div class="w-100 flex-grow-1 checklist-flex-fix" id="activitiesTableWrapper">
      <div id="activitiesSkeleton" class="position-absolute top-0 start-0 w-100 h-100 bg-dash-dark-2">
        @for ($i = 0; $i < 6; $i++)
          <div class="d-flex justify-content-between align-items-center mb-3 px-2">
            <div class="skeleton" style="width: 7%; height: 28px; border-radius: 6px;"></div>
            <div class="skeleton" style="width: 10%; height: 14px;"></div>
            <div class="skeleton" style="width: 24%; height: 14px;"></div>
            <div class="skeleton" style="width: 10%; height: 14px;"></div>
            <div class="skeleton" style="width: 18%; height: 14px;"></div>
            <div class="skeleton" style="width: 14%; height: 14px;"></div>
          </div>
        @endfor
      </div>
      <table class="table table-striped table-dark text-center nowrap" id="activities">
        <thead>
          <tr>
            <th></th>
            <th>ODO</th>
            <th>Location</th>
            <th>Conditon</th>
            <th>Created By</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
