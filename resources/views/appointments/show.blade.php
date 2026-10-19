@extends('layouts.app')

@section('title', 'Appointment Details')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Appointment Details',
    'breadcrumb' => 'Appointment Details',
])
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-sm-flex justify-content-end mb-2">
    <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left fa-sm me-1"></i> Back to Appointments
    </a>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="sqa-form-card mb-4">
            <h2 class="sqa-section-header">Appointment Information</h2>
            <div class="sqa-form-body py-3">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <small class="text-uppercase text-muted d-block">Reference</small>
                        <strong>{{ $appointment->appointment_number }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-uppercase text-muted d-block">Date</small>
                        <span>{{ $appointment->appointment_date->format('d M Y, l') }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-uppercase text-muted d-block">Time Slot</small>
                        <span>
                            {{ \Carbon\Carbon::parse($appointment->slot_time)->format('g:i A') }}
                            - {{ \Carbon\Carbon::parse($appointment->slot_end_time)->format('g:i A') }}
                            ({{ $appointment->slot_duration_minutes }} min)
                        </span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-uppercase text-muted d-block">Doctor</small>
                        <span>{{ $appointment->doctor->name }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-uppercase text-muted d-block">Department</small>
                        <span>{{ $appointment->department }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-uppercase text-muted d-block">Status</small>
                        <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="sqa-form-card">
            <h2 class="sqa-section-header">Patient Information</h2>
            <div class="sqa-form-body py-3">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <small class="text-uppercase text-muted d-block">Name</small>
                        <span>{{ $appointment->patient->full_name }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-uppercase text-muted d-block">Patient ID</small>
                        @if($appointment->patient->pid)
                            <span class="badge bg-success">{{ $appointment->patient->pid }}</span>
                        @elseif($appointment->patient->temp_number)
                            <span class="badge bg-warning text-dark">{{ $appointment->patient->temp_number }}</span>
                            <small class="d-block text-muted mt-1">Temp — check in on arrival for PID</small>
                        @else
                            <span>#{{ $appointment->patient->id }}</span>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-uppercase text-muted d-block">Phone</small>
                        <span>{{ $appointment->patient->phone }}</span>
                    </div>
                </div>

                @if($appointment->patient->isTemp())
                    <div class="alert alert-info mt-2 mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Share temp number <strong>{{ $appointment->patient->temp_number }}</strong> with the patient.
                        On arrival, use <a href="{{ route('appointments.check-in') }}">Patient Check-In</a> to generate the permanent PID.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
