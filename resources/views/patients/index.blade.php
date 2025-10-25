@extends('layouts.app')

@section('title', 'Patients')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Patient Management</h1>
    <a href="{{ route('patients.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-user-plus fa-sm text-white-50 me-1"></i> Register New Patient
    </a>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Patient List</h6>
                
                <!-- Search Form -->
                <form method="GET" action="{{ route('patients.index') }}" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control bg-light border-0 small" 
                               placeholder="Search by ID or Name..." 
                               value="{{ $search ?? '' }}"
                               aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <!-- Success Alert with Auto-dismiss -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show auto-dismiss-alert" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" onclick="closeAlert(this)"></button>
                    </div>
                @endif

                <!-- Search Results Alert with Auto-dismiss -->
                @if(isset($search) && $search && $patients->count() > 0)
                    <div class="alert alert-info alert-dismissible fade show auto-dismiss-alert" role="alert">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-search me-2"></i>
                                <div>Showing results for: "<strong>{{ $search }}</strong>"</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-info me-2">
                                    <i class="fas fa-times me-1"></i> Clear Search
                                </a>
                                <button type="button" class="btn-close" onclick="closeAlert(this)"></button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Only show table if patients exist -->
                @if($patients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="patientsTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="80px">ID</th>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th width="140px" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                            <tr>
                                <td>
                                    <strong class="text-primary">#{{ $patient->id }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $patient->first_name }} {{ $patient->last_name }}</strong>
                                </td>
                                <td>{{ $patient->date_of_birth->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-info text-white">{{ $patient->date_of_birth->age }} years</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $patient->gender == 'male' ? 'primary' : ($patient->gender == 'female' ? 'danger' : 'secondary') }}">
                                        <i class="fas fa-{{ $patient->gender == 'male' ? 'mars' : ($patient->gender == 'female' ? 'venus' : 'genderless') }} me-1"></i>
                                        {{ ucfirst($patient->gender) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-phone text-muted me-1"></i>{{ $patient->phone }}
                                </td>
                                <td>
                                    @if($patient->email)
                                        <i class="fas fa-envelope text-muted me-1"></i>
                                        <small>{{ $patient->email }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- View Button -->
                                        <a href="{{ route('patients.show', $patient->id) }}" 
                                           class="btn btn-info btn-sm px-2" 
                                           title="View Patient Details"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-eye fa-fw"></i>
                                        </a>
                                        
                                        <!-- Edit Button -->
                                        <a href="{{ route('patients.edit', $patient->id) }}" 
                                           class="btn btn-warning btn-sm px-2" 
                                           title="Edit Patient"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit fa-fw"></i>
                                        </a>
                                        
                                        <!-- Delete Button -->
                                        <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm px-2" 
                                                    title="Delete Patient"
                                                    data-bs-toggle="tooltip"
                                                    onclick="return confirm('Are you sure you want to delete patient #{{ $patient->id }}? This action cannot be undone.')">
                                                <i class="fas fa-trash fa-fw"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Search Help Text -->
                @if(!isset($search) || !$search)
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-search me-1"></i>
                            Use the search box above to find patients by ID, first name, or last name
                        </small>
                    </div>
                @endif

                @else
                    <!-- No patients found - Immediate redirect -->
                    @if(isset($search) && $search)
                        <!-- Search with no results - Immediate redirect -->
                        <div class="text-center py-5">
                            <div class="alert alert-warning">
                                <i class="fas fa-search fa-2x mb-3"></i>
                                <h4>No patients found for "{{ $search }}"</h4>
                                <p class="mb-3">Redirecting to patient registration...</p>
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        
                        <script>
                            // Immediate redirect to patient registration
                            window.location.href = "{{ route('patients.create') }}?search={{ urlencode($search) }}";
                        </script>
                    
                    @else
                        <!-- No patients at all (not a search) -->
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                <h4>No Patients Found</h4>
                                <p class="mb-4">Get started by registering your first patient.</p>
                                <a href="{{ route('patients.create') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Register First Patient
                                </a>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }
    .navbar-search {
        width: 300px;
    }
    .auto-dismiss-alert {
        transition: opacity 0.5s ease-in-out;
    }
</style>
@endpush

@push('scripts')
<script>
    // Simple alert close function
    function closeAlert(button) {
        const alert = button.closest('.alert');
        if (alert) {
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }
    }

    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.auto-dismiss-alert');
        
        alerts.forEach(function(alert) {
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 500);
                }
            }, 5000);
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Clear search when pressing Escape in search field
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    window.location.href = "{{ route('patients.index') }}";
                }
            });
        }
    });
</script>
@endpush