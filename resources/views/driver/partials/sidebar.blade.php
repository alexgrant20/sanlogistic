<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-body">
    <img class="rounded-circle mb-3"
      src="{{ auth()->user()->person->image ? asset('/storage/' . auth()->user()->person->image) : asset('/img/default.jpg') }}"
      alt="" width="75" height="75">
    <h4>{{ auth()->user()->person->name }}</h4>
    <hr class="mb-5">

    {{-- Menu --}}

    {{-- Home --}}
    <a href="{{ route('index') }}"
      class="d-flex align-items-center mb-4 py-2 px-3 {{ Request::is('/') ? 'bg-primary' : '' }}  fs-4 rounded fw-bold">
      <i class="bi bi-house-door fs-3 me-5"></i>
      {{ __('Home') }}
    </a>

    {{-- Profile --}}
    <a href="{{ route('driver.menu.profile') }}"
      class="d-flex align-items-center mb-4 py-2 px-3 {{ Request::is('driver/profile') ? 'bg-primary' : '' }} fs-4 rounded fw-bold">
      <i class="bi bi-person fs-3 me-5"></i>
      {{ __('Profile') }}
    </a>

    {{-- History --}}
    <a href="{{ route('driver.activity.index') }}"
      class="d-flex align-items-center mb-4 py-2 px-3 {{ Request::is('driver/activities') ? 'bg-primary' : '' }} fs-4 rounded fw-bold">
      <i class="fa-solid fa-clock-rotate-left fs-3 me-5"></i>
      {{ __('History') }}
    </a>

    {{-- Activity --}}
    @php
      $activityId = is_null(session()->get('activity_id'));
    @endphp
    <a href="{{ $activityId ? route('driver.activity.create') : route('driver.activity.edit', session()->get('activity_id')) }}"
      class="d-flex align-items-center mb-4 py-2 px-3 {{ Request::is('driver/activities/*') ? 'bg-primary' : '' }} fs-4 rounded fw-bold">
      <i class="{{ $activityId ? 'bi-clipboard-plus-fill' : 'bi-flag' }} fs-3 me-5"></i>
      {{ __('Activity') }}
    </a>

    {{-- Location --}}
    <a href="{{ route('driver.menu.location') }}"
      class="d-flex align-items-center mb-4 py-2 px-3 {{ Request::is('driver/location') ? 'bg-primary' : '' }} fs-4 rounded fw-bold">
      <i class="bi bi-map fs-3 me-5"></i>
      {{ __('Location') }}
    </a>

    {{-- Location --}}
    <a href="{{ route('driver.menu.checklist') }}"
      class="d-flex align-items-center mb-4 py-2 px-3 {{ Request::is('driver/checklist') ? 'bg-primary' : '' }} fs-4 rounded fw-bold">
      <i class="bi bi-clipboard2-check-fill fs-3 me-5"></i>
      {{ __('Checklist') }}
    </a>

    {{-- Finances --}}
    <a href="#"
      class="d-flex align-items-center mb-4 py-2 px-3 {{ Request::is('driver/finance') ? 'bg-primary' : '' }} fs-4 rounded fw-bold">
      <i class="bi bi-cash-coin fs-3 me-5"></i>
      {{ __('Finance') }}
    </a>

  </div>
</div>