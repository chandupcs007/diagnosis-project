<!-- Sidebar -->
<div class="navbar-nav bg-gradient-primary sidebar sidebar-dark" id="sidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center mb-4" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-stethoscope"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ config('diagnosis.name') }}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @inject('menuService', 'App\Services\MenuService')
    @php
        $currentRoute = request()->route()->getName();
        $menuItems = $menuService->getMenuItems();
    @endphp

    <!-- Nav Items -->
    <ul class="nav flex-column">
        @foreach($menuItems as $menuItem)
            <li class="nav-item">
                <a class="nav-link {{ $currentRoute == $menuItem->route ? 'active' : '' }}" 
                   href="{{ $menuItem->route ? route($menuItem->route) : '#' }}">
                    <i class="{{ $menuItem->icon }} me-2"></i>
                    <span>{{ $menuItem->name }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline mt-4">
        <button class="rounded-circle border-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

</div>
<!-- End of Sidebar -->