<nav id="sidebar" class="pb-4">
  <!-- Sidebar Header-->
  <div class="sidebar-header d-flex align-items-center p-4">
    <img class="avatar shadow-0 img-fluid rounded-circle"
      src="{{ optional(auth()->user()->person)->image ? asset('/storage/' . auth()->user()->person->image) : asset('/img/default.jpg') }}"
      alt="">
    <div class="ms-3 title">
      <h1 class="h5 mb-1">{{ auth()->user()->username }}</h1>
      <p class="text-sm text-capitalize text-gray-700 mb-0 lh-1">{{ auth()->user()->roles->pluck('name')[0] }}</p>
    </div>
  </div>

  <span class="text-uppercase text-gray-600 text-xs heading mb-2 mx-3 text-wrap d-block">General</span>
  <ul class="list-unstyled">

    <li class="sidebar-item {{ Request::is('/') ? 'active' : '' }}">
      <a class="sidebar-link" href="{{ route('index') }}">
        <i class="fa-solid fa-house svg-icon svg-icon-sm"></i>
        <span>Home</span>
      </a>
    </li>
  </ul>

  <span class="text-uppercase text-gray-600 text-xs heading mb-2 mx-3 text-wrap d-block">Super Admin</span>
  <ul class="list-unstyled">
    @canany(['address-create', 'address-view'])
      <li class="sidebar-item {{ Request::is('admin/addresses*') ? 'active' : '' }}">
        <a class="sidebar-link" href="#addressesDropDown" data-bs-toggle="collapse">
          <i class="fa-solid fa-address-book svg-icon svg-icon-sm"></i>
          <span>Addresses</span>
        </a>
        <ul class="collapse list-unstyled {{ Request::is('admin/addresses*') ? 'show' : '' }}" id="addressesDropDown">
          @can('address-create')
            <li>
              <a class="sidebar-link {{ Route::is('admin.addresses.create') ? 'text-primary' : '' }}"
                href="{{ route('admin.addresses.create') }}">
                Add
              </a>
            </li>
          @endcan
          @can('address-view')
            <li>
              <a class="sidebar-link {{ Route::is('admin.addresses.index') ? 'text-primary' : '' }}"
                href="{{ route('admin.addresses.index') }}">
                View
              </a>
            </li>
          @endcan
        </ul>
      </li>
    @endcanany

    @canany(['activity-create', 'activity-view', 'finance-acceptance'])
      <li class="sidebar-item {{ Request::is('admin/activities*') ? 'active' : '' }}">
        <a class="sidebar-link" href="#activitiesDropDown" data-bs-toggle="collapse">
          <i class="fa-solid fa-memo-circle-info svg-icon svg-icon-sm"></i>
          <span>Activities</span>
        </a>
        <ul class="collapse list-unstyled {{ Request::is('admin/activities*') ? 'show' : '' }}" id="activitiesDropDown">
          @can('activity-create')
            <li>
              <a class="sidebar-link {{ Route::is('admin.activities.create') ? 'text-primary' : '' }}"
                href="{{ route('admin.activities.create') }}">
                Add
              </a>
            </li>
          @endcan
          @can('activity-view')
            <li>
              <a class="sidebar-link {{ Route::is('admin.activities.index') ? 'text-primary' : '' }}"
                href="{{ route('admin.activities.index') . '?status=pending' }}">
                View
              </a>
            </li>
          @endcan
          @can('finance-acceptance')
            <li>
              <a class="sidebar-link {{ Route::is('admin.activities.approval') ? 'text-primary' : '' }}"
                href="{{ route('admin.activities.approval') }}">
                Payment Submission
              </a>
            </li>
          @endcan
        </ul>
      </li>
    @endcanany

    @canany(['person-create', 'person-view'])
      <li class="sidebar-item {{ Request::is('admin/people*') ? 'active' : '' }}">
        <a class="sidebar-link" href="#peopleDropDown" data-bs-toggle="collapse">
          <i class="fa-regular fa-users svg-icon svg-icon-sm"></i>
          <span>People</span>
        </a>
        <ul class="collapse list-unstyled {{ Request::is('admin/people*') ? 'show' : '' }}" id="peopleDropDown">
          @can('person-create')
            <li>
              <a class="sidebar-link {{ Route::is('admin.people.create') ? 'text-primary' : '' }}"
                href="{{ route('admin.people.create') }}">
                Add
              </a>
            </li>
          @endcan
          @can('person-view')
            <li>
              <a class="sidebar-link {{ Route::is('admin.people.index') ? 'text-primary' : '' }}"
                href="{{ route('admin.people.index') }}">
                View
              </a>
            </li>
          @endcan
          @can('person-driver-finance')
            <li>
              <a class="sidebar-link {{ Route::is('admin.driver.finance') ? 'text-primary' : '' }}"
                href="{{ route('admin.people.finance') }}">
                Driver Finance
              </a>
            </li>
          @endcan
        </ul>
      </li>
    @endcanany

    @canany(['user-role-create', 'user-role-view'])
      <li class="sidebar-item {{ Request::is('admin/roles*') ? 'active' : '' }}">
        <a class="sidebar-link" href="#rolesDropDown" data-bs-toggle="collapse">
          <i class="fa-solid fa-user-gear svg-icon svg-icon-sm"></i>
          <span>Roles</span>
        </a>
        <ul class="collapse list-unstyled {{ Request::is('admin/roles*') ? 'show' : '' }}" id="rolesDropDown">
          @can('user-role-create')
            <li>
              <a class="sidebar-link {{ Route::is('admin.roles.create') ? 'text-primary' : '' }}"
                href="{{ route('admin.roles.create') }}">
                Add
              </a>
            </li>
          @endcan
          @can('user-role-view')
            <li>
              <a class="sidebar-link {{ Route::is('admin.roles.index') ? 'text-primary' : '' }}"
                href="{{ route('admin.roles.index') }}">
                View
              </a>
            </li>
          @endcan
        </ul>
      </li>

      {{-- <li class="sidebar-item {{ Request::is('admin/permission*') ? 'active' : '' }}">
        <a class="sidebar-link" href="#permissionDropDown" data-bs-toggle="collapse">
          <i class="fa-solid fa-gavel svg-icon svg-icon-sm"></i>
          <span>Permission</span>
        </a>
        <ul class="collapse list-unstyled {{ Request::is('admin/permission*') ? 'show' : '' }}" id="permissionDropDown">
          <li>
            <a class="sidebar-link {{ Route::is('admin.permissions.create') ? 'text-primary' : '' }}"
              href="{{ route('admin.permissions.create') }}">
              Add
            </a>
          </li>
          <li>
            <a class="sidebar-link {{ Route::is('admin.permissions.index') ? 'text-primary' : '' }}"
              href="{{ route('admin.permissions.index') }}">
              View
            </a>
          </li>
        </ul>
      </li> --}}
    @endcan

    @canany(['vehicle-create', 'vehicle-view', 'vehicle-condition-view'])
      <li class="sidebar-item {{ Request::is('admin/vehicles*') ? 'active' : '' }}">
        <a class="sidebar-link" href="#vehiclesDropDown" data-bs-toggle="collapse">
          <i class="fa-solid fa-truck svg-icon svg-icon-sm"></i>
          <span>Vehicles</span>
        </a>
        <ul class="collapse list-unstyled {{ Request::is('admin/vehicles*') ? 'show' : '' }}" id="vehiclesDropDown">
          @can('vehicle-create')
            <li>
              <a class="sidebar-link {{ Route::is('admin.vehicles.create') ? 'text-primary' : '' }}"
                href="{{ route('admin.vehicles.create') }}">
                Add
              </a>
            </li>
          @endcan
          @can('vehicle-view')
            <li>
              <a class="sidebar-link {{ Route::is('admin.vehicles.index') ? 'text-primary' : '' }}"
                href="{{ route('admin.vehicles.index') }}">
                View
              </a>
            </li>
          @endcan
          @can('vehicle-condition-view')
            <li>
              <a class="sidebar-link {{ Route::is('admin.vehicles-last-statuses.index') ? 'text-primary' : '' }}"
                href="{{ route('admin.vehicles-last-statuses.index') }}">
                View Condition
              </a>
            </li>
          @endcan
        </ul>
      </li>
    @endcanany

    @canany(['company-create', 'company-view'])
      <li class="sidebar-item {{ Request::is('admin/companies*') ? 'active' : '' }}">
        <a class="sidebar-link" href="#companiesDropDown" data-bs-toggle="collapse">
          <i class="fa-regular fa-building-memo svg-icon svg-icon-sm"></i>
          <span>Companies</span>
        </a>
        <ul class="collapse list-unstyled {{ Request::is('admin/companies*') ? 'show' : '' }}" id="companiesDropDown">
          @can('company-create')
            <li>
              <a class="sidebar-link {{ Route::is('admin.companies.create') ? 'text-primary' : '' }}"
                href="{{ route('admin.companies.create') }}">
                Add
              </a>
            </li>
          @endcan
          @can('company-view')
            <li>
              <a class="sidebar-link {{ Route::is('admin.companies.index') ? 'text-primary' : '' }}"
                href="{{ route('admin.companies.index') }}">
                View
              </a>
            </li>
          @endcan
        </ul>
      </li>
    @endcanany

    @canany(['project-create', 'project-view'])
      <li class="sidebar-item {{ Request::is('admin/projects*') ? 'active' : '' }}">
        <a class="sidebar-link" href="#projectsDropDown" data-bs-toggle="collapse">
          <i class="fa-solid fa-square-kanban svg-icon svg-icon-sm"></i>
          <span>Projects</span>
        </a>
        <ul class="collapse list-unstyled {{ Request::is('admin/projects*') ? 'show' : '' }}" id="projectsDropDown">
          @can('project-create')
            <li>
              <a class="sidebar-link {{ Route::is('admin.projects.create') ? 'text-primary' : '' }}"
                href="{{ route('admin.projects.create') }}">
                Add
              </a>
            </li>
          @endcan
          @can('project-view')
            <li>
              <a class="sidebar-link {{ Route::is('admin.projects.index') ? 'text-primary' : '' }}"
                href="{{ route('admin.projects.index') }}">
                View
              </a>
            </li>
          @endcan
        </ul>
      </li>
    @endcanany

    @canany(['finance-payment'])
      <li class="sidebar-item {{ Request::is('admin/finances*') ? 'active' : '' }}">
        <a class="sidebar-link" href="#financesDropDown" data-bs-toggle="collapse">
          <i class="fa-light fa-chart-mixed-up-circle-dollar svg-icon svg-icon-sm"></i>
          <span>Finance</span>
        </a>
        <ul class="collapse list-unstyled {{ Request::is('admin/finances*') ? 'show' : '' }}" id="financesDropDown">

          @can('finance-payment')
            <li>
              <a class="sidebar-link {{ Route::is('admin.finances.payment') ? 'text-primary' : '' }}"
                href="{{ route('admin.finances.payment') }}">
                Payment
              </a>
            </li>
          @endcan
        </ul>
      </li>
    @endcanany

    <li class="sidebar-item {{ Request::is('admin/areas*') ? 'active' : '' }}">
      <a class="sidebar-link" href="#areasDropDown" data-bs-toggle="collapse">
        <i class="fa-solid fa-map-location svg-icon svg-icon-sm"></i>
        <span>Area</span>
      </a>
      <ul class="collapse list-unstyled {{ Request::is('admin/areas*') ? 'show' : '' }}" id="areasDropDown">
        @can('finance-acceptance')
          <li>
            <a class="sidebar-link {{ Route::is('admin.areas.approval') ? 'text-primary' : '' }}"
              href="{{ route('admin.areas.create') }}">
              Add
            </a>
          </li>
        @endcan
        @can('finance-payment')
          <li>
            <a class="sidebar-link {{ Route::is('admin.areas.payment') ? 'text-primary' : '' }}"
              href="{{ route('admin.areas.index') }}">
              View
            </a>
          </li>
        @endcan
      </ul>
    </li>
  </ul>

</nav>
