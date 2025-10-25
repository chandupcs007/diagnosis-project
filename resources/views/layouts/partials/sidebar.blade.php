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

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Patient Management
    </div>

    <!-- Nav Item - Patients -->
    <li class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('patients.index') }}">
            <i class="fas fa-fw fa-user-injured"></i>
            <span>Patients</span>
        </a>
    </li>

    <!-- Nav Item - Diagnosis -->
    <li class="nav-item {{ request()->routeIs('diagnosis.*') ? 'active' : '' }}">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-diagnoses"></i>
            <span>Diagnosis</span>
        </a>
    </li>

    <!-- Nav Item - Reports -->
    <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Reports</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->