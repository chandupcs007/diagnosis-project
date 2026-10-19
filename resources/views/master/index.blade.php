@extends('layouts.app')

@section('title', 'Master')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Master',
    'breadcrumb' => 'Master',
])
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <a href="{{ route('master.users.create') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100 border-left-primary master-option-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Create User</div>
                            <div class="small text-muted">Add admin, staff, or user accounts</div>
                        </div>
                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <a href="{{ route('master.services.create') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100 border-left-success master-option-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Create Service</div>
                            <div class="small text-muted">Manage billing service catalog</div>
                        </div>
                        <i class="fas fa-concierge-bell fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <a href="{{ route('master.hospital.edit') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100 border-left-info master-option-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Hospital Details</div>
                            <div class="small text-muted">Name, logo, address, and contacts</div>
                        </div>
                        <i class="fas fa-hospital fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <a href="{{ route('master.doctors.create') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100 border-left-warning master-option-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Create Doctor</div>
                            <div class="small text-muted">Register a new doctor account</div>
                        </div>
                        <i class="fas fa-user-md fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <a href="{{ route('master.lis-mapping.index') }}" class="text-decoration-none">
            <div class="card shadow-sm h-100 border-left-danger master-option-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">LIS Mapping</div>
                            <div class="small text-muted">Map lab tests to LIS / instrument codes</div>
                        </div>
                        <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    @if(!empty($showPhpMyAdmin))
        <div class="col-md-6 col-lg-3 mb-3">
            <a href="{{ route('master.phpmyadmin') }}" class="text-decoration-none" target="_blank" rel="noopener noreferrer">
                <div class="card shadow-sm h-100 border-left-secondary master-option-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">phpMyAdmin</div>
                                <div class="small text-muted">Open MySQL database manager</div>
                            </div>
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h6 class="font-weight-bold text-gray-800 mb-2">Master Data Management</h6>
        <p class="text-muted mb-0">
            Use the options above to configure hospital information, create system users, register doctors, and maintain the service catalog used during visit billing.
        </p>
    </div>
</div>
@endsection

@push('styles')
<style>
    .master-option-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .master-option-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12) !important;
    }
</style>
@endpush
