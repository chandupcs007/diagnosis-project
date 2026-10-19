<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-logo">
            <img src="{{ asset('images/carevision-brand.jpg') }}" alt="{{ config('diagnosis.name') }}">
        </div>
        <div class="sidebar-brand-text mx-2">{{ config('diagnosis.name') }}</div>
    </a>

    <!-- Profile Section -->
    <div class="sidebar-profile">
        <div class="sidebar-profile-inner">
            <div class="sidebar-profile-avatar{{ auth()->user()?->photo_url ? ' has-photo' : '' }}">
                @auth
                    @if(auth()->user()->photo_url)
                        <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}">
                    @else
                        <i class="fas fa-user-md"></i>
                    @endif
                @else
                    <i class="fas fa-user-md"></i>
                @endauth
            </div>
            <div class="sidebar-profile-info">
                <div class="sidebar-profile-name">
                    @auth
                        {{ auth()->user()->name }}
                    @else
                        Guest
                    @endauth
                </div>
                <div class="sidebar-profile-dept">
                    @auth
                        {{ auth()->user()->display_department }}
                    @else
                        {{ $hospitalSetting->department_name }}
                    @endauth
                </div>
            </div>
        </div>
    </div>

    @inject('menuService', 'App\Services\MenuService')
    @php
        $currentRoute = request()->route()->getName();
        $menuItems = $menuService->getMenuItems();
    @endphp

    <!-- Nav Items -->
    @foreach($menuItems as $menuItem)
        @php
            $isActive = $currentRoute == $menuItem->route
                || ($menuItem->route === 'laboratory.index' && str_starts_with($currentRoute ?? '', 'laboratory.'))
                || ($menuItem->route === 'pharmacy.index' && str_starts_with($currentRoute ?? '', 'pharmacy.'))
                || ($menuItem->route === 'master.index' && str_starts_with($currentRoute ?? '', 'master.'))
                || ($menuItem->route === 'billing.index' && str_starts_with($currentRoute ?? '', 'billing.'));
        @endphp
        <li class="nav-item">
            <a class="nav-link {{ $isActive ? 'active' : '' }}"
               href="{{ $menuItem->route ? route($menuItem->route) : '#' }}">
                <i class="{{ $menuItem->icon }} fa-fw"></i>
                <span>{{ $menuItem->name }}</span>
            </a>
        </li>
    @endforeach

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

</ul>
<!-- End of Sidebar -->
