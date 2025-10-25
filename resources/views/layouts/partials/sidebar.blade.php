<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
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

    @foreach($menuItems as $menuItem)
        @if($menuItem->children->count() > 0)
            <!-- Nav Item - Collapsible Menu -->
            <li class="nav-item {{ $menuService->isActive($menuItem, $currentRoute) ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse{{ $menuItem->id }}" 
                   aria-expanded="true" aria-controls="collapse{{ $menuItem->id }}">
                    <i class="{{ $menuItem->icon }}"></i>
                    <span>{{ $menuItem->name }}</span>
                </a>
                <div id="collapse{{ $menuItem->id }}" class="collapse" aria-labelledby="heading{{ $menuItem->id }}" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @foreach($menuItem->children as $child)
                            <a class="collapse-item {{ $currentRoute == $child->route ? 'active' : '' }}" 
                               href="{{ $child->route ? route($child->route) : '#' }}">
                                <i class="{{ $child->icon }} me-2"></i>
                                {{ $child->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </li>
        @else
            <!-- Nav Item - Single Menu -->
            <li class="nav-item {{ $currentRoute == $menuItem->route ? 'active' : '' }}">
                <a class="nav-link" href="{{ $menuItem->route ? route($menuItem->route) : '#' }}">
                    <i class="{{ $menuItem->icon }}"></i>
                    <span>{{ $menuItem->name }}</span>
                </a>
            </li>
        @endif

        <!-- Divider -->
        <hr class="sidebar-divider">
    @endforeach

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->