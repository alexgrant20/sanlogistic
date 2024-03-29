<header class="d-flex flex-column justify-content-between align-items-center shadow-sm">
  <div class="d-flex justify-content-between align-items-center w-100 p-3">
    {{-- @if (!Request::is('/'))
      <a href="{{ route('index') }}" style="cursor: pointer;">
        <i class='bi bi-caret-left-fill fs-3'></i>
      </a>
    @else --}}
    <a data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
      <i class="bi bi-list fs-1"></i>
    </a>
    @include('driver.partials.sidebar')
    {{-- @endif --}}
    <span class="fs-4 fw-bold {{ Request::is('/') ? 'text-center' : 'ms-3' }}" style="flex: 1">
      @yield('title')
    </span>
  </div>
</header>
