<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light topbar static-top sqa-topbar">
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle sqa-topbar-toggle" type="button" aria-label="Toggle sidebar">
        <i class="fa fa-bars"></i>
    </button>

    <div class="sqa-topbar-brand ms-2 ms-md-3">
        @if($hospitalSetting->logo_url)
            <img src="{{ $hospitalSetting->logo_url }}"
                 alt="{{ $hospitalSetting->hospital_name }}"
                 class="sqa-topbar-logo">
        @else
            <div class="sqa-topbar-logo-fallback" aria-hidden="true">
                <i class="fas fa-hospital"></i>
            </div>
        @endif
        <div class="sqa-topbar-hospital">
            <span class="sqa-topbar-hospital-name">{{ $hospitalSetting->hospital_name }}</span>
        </div>
    </div>

    <ul class="navbar-nav ms-auto align-items-center flex-row">
        @if(Auth::check())
        <li class="nav-item me-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-fullscreen-toggle aria-label="Enter full screen" title="Full screen (F11)">
                <i class="fas fa-expand fa-sm"></i>
            </button>
        </li>
        <li class="nav-item me-3">
            <span class="navbar-text">
                {{ Auth::user()->name }}
            </span>
        </li>
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-sign-out-alt fa-sm"></i> Logout
                </button>
            </form>
        </li>
        @else
        <li class="nav-item">
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-sign-in-alt fa-sm"></i> Login
            </a>
        </li>
        @endif
    </ul>
</nav>
<!-- End of Topbar -->
