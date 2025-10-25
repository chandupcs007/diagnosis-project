<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        @if(Auth::check())
        <li class="nav-item">
            <span class="navbar-text mr-3">
                Welcome, {{ Auth::user()->name }}
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