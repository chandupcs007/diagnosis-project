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
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Patient List</h6>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="patientsTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
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
                            @if($patients->count() > 0)
                                @foreach($patients as $patient)
                                <tr>
                                    <td><strong>#{{ $patient->id }}</strong></td>
                                    <td>
                                        <strong>{{ $patient->first_name }} {{ $patient->last_name }}</strong>
                                    </td>
                                    <td>{{ $patient->date_of_birth->format('M d, Y') }}</td>
                                    <td><span class="badge bg-info text-white">{{ $patient->date_of_birth->age }} years</span></td>
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
                                            <i class="fas fa-envelope text-muted me-1"></i>{{ $patient->email }}
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
                            @else
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                            <h5>No Patients Found</h5>
                                            <p class="mb-3">Get started by registering your first patient.</p>
                                            <a href="{{ route('patients.create') }}" class="btn btn-primary">
                                                <i class="fas fa-user-plus me-2"></i>Register First Patient
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }
</style>
@endpush

@push('scripts')
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush