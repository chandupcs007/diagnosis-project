<div class="mb-4">
    <div class="btn-group flex-wrap" role="group" aria-label="Master navigation">
        <a href="{{ route('master.index') }}"
           class="btn btn-sm {{ request()->routeIs('master.index') ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-cogs me-1"></i> Master Home
        </a>
        <a href="{{ route('master.users.create') }}"
           class="btn btn-sm {{ request()->routeIs('master.users.*') ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-user-plus me-1"></i> Create User
        </a>
        <a href="{{ route('master.services.create') }}"
           class="btn btn-sm {{ request()->routeIs('master.services.*') ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-concierge-bell me-1"></i> Create Service
        </a>
        <a href="{{ route('master.hospital.edit') }}"
           class="btn btn-sm {{ request()->routeIs('master.hospital.*') ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-hospital me-1"></i> Hospital Details
        </a>
        <a href="{{ route('master.doctors.create') }}"
           class="btn btn-sm {{ request()->routeIs('master.doctors.*') ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-user-md me-1"></i> Create Doctor
        </a>
        <a href="{{ route('master.lis-mapping.index') }}"
           class="btn btn-sm {{ request()->routeIs('master.lis-mapping.*') ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-project-diagram me-1"></i> LIS Mapping
        </a>
        @if(auth()->check() && config('diagnosis.phpmyadmin.enabled') && config('diagnosis.phpmyadmin.url'))
            <a href="{{ route('master.phpmyadmin') }}"
               class="btn btn-sm btn-outline-secondary"
               target="_blank"
               rel="noopener noreferrer">
                <i class="fas fa-database me-1"></i> phpMyAdmin
            </a>
        @endif
    </div>
</div>
