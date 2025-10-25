@extends('layouts.app')

@section('title', 'Visit Details')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Visit Details</h1>
    <div>
        <a href="{{ route('visits.create', $visit->patient_id) }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
            <i class="fas fa-calendar-plus fa-sm text-white-50"></i> New Visit
        </a>
        <a href="{{ route('patients.show', $visit->patient_id) }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
            <i class="fas fa-user fa-sm text-white-50"></i> Patient Details
        </a>
        <a href="{{ route('patients.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Patients
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-medical me-2"></i>Visit Information
                </h6>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Visit Number:</th>
                                <td>
                                    <span class="badge bg-primary fs-6">{{ $visit->visit_number }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Visit Date:</th>
                                <td>{{ $visit->visit_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Visit Time:</th>
                                <td>{{ date('h:i A', strtotime($visit->visit_time)) }}</td>
                            </tr>
                            <tr>
                                <th>Department:</th>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $visit->department }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Status:</th>
                                <td>
                                    @php
                                        $statusColors = [
                                            'scheduled' => 'warning',
                                            'in_progress' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$visit->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Doctor:</th>
                                <td>
                                    <strong>Dr. {{ $visit->doctor->name }}</strong>
                                    @if($visit->doctor->specialization)
                                        <br><small class="text-muted">{{ $visit->doctor->specialization }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created On:</th>
                                <td>{{ $visit->created_at->format('F d, Y \a\t h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($visit->symptoms)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold text-primary">
                            <i class="fas fa-stethoscope me-2"></i>Symptoms / Chief Complaints
                        </h6>
                        <div class="p-3 bg-light rounded">
                            {{ $visit->symptoms }}
                        </div>
                    </div>
                </div>
                @endif

                @if($visit->diagnosis)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold text-primary">
                            <i class="fas fa-diagnoses me-2"></i>Diagnosis
                        </h6>
                        <div class="p-3 bg-light rounded">
                            {{ $visit->diagnosis }}
                        </div>
                    </div>
                </div>
                @endif

                @if($visit->prescription)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold text-primary">
                            <i class="fas fa-prescription me-2"></i>Prescription
                        </h6>
                        <div class="p-3 bg-light rounded">
                            {{ $visit->prescription }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Patient Information Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-user-injured me-2"></i>Patient Information
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <th>Name:</th>
                        <td>
                            <strong>{{ $visit->patient->first_name }} {{ $visit->patient->last_name }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <th>Patient ID:</th>
                        <td>#{{ $visit->patient->id }}</td>
                    </tr>
                    <tr>
                        <th>Age:</th>
                        <td>{{ $visit->patient->date_of_birth->age }} years</td>
                    </tr>
                    <tr>
                        <th>Gender:</th>
                        <td>{{ ucfirst($visit->patient->gender) }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $visit->patient->phone }}</td>
                    </tr>
                    @if($visit->patient->email)
                    <tr>
                        <th>Email:</th>
                        <td>{{ $visit->patient->email }}</td>
                    </tr>
                    @endif
                </table>

                <div class="text-center mt-3">
                    <a href="{{ route('patients.show', $visit->patient_id) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-user me-1"></i>View Full Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection