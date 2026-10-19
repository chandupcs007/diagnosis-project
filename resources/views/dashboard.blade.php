@extends('layouts.app')

@section('title', 'Dashboard')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => config('diagnosis.name') . ' Dashboard',
    'breadcrumb' => 'Dashboard',
])
@endpush

@section('content')
<div class="sqa-dashboard">
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Patients</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($patientCount) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-injured fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Visits Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($visitsToday) }}</div>
                        <div class="small text-muted">{{ $diagnosisToday }} diagnosis recorded</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-stethoscope fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Cases</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingCases) }}</div>
                        <div class="small text-muted">{{ $pendingLabRequests }} lab · {{ $pendingAppointments }} appt</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Lab Completion</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $successRate }}%</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar"
                                         style="width: {{ $successRate }}%"
                                         aria-valuenow="{{ $successRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-flask fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Patients</h6>
                <a href="{{ route('patients.index') }}" class="small">View all</a>
            </div>
            <div class="card-body p-0">
                @if($recentPatients->count())
                    <div class="list-group list-group-flush">
                        @foreach($recentPatients as $patient)
                            <a href="{{ route('patients.show', $patient) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $patient->full_name }}</strong>
                                    <small class="text-muted">{{ $patient->display_id }}</small>
                                </div>
                                <small class="text-muted">{{ $patient->created_at->format('d M Y') }}</small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 text-muted">No patients registered yet.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Pending Lab Requests</h6>
                <a href="{{ route('laboratory.index') }}" class="small">Open lab</a>
            </div>
            <div class="card-body p-0">
                @if($recentLabRequests->count())
                    <div class="list-group list-group-flush">
                        @foreach($recentLabRequests as $request)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $request->test_name }}</strong>
                                    <span class="badge bg-warning text-dark">{{ $request->status_label }}</span>
                                </div>
                                <small class="text-muted">
                                    {{ $request->visit->patient->full_name ?? '—' }} · {{ $request->request_number }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 text-muted">No pending lab requests.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Today's Appointments</h6>
                <a href="{{ route('appointments.index') }}" class="small">View all</a>
            </div>
            <div class="card-body p-0">
                @if($todayAppointments->count())
                    <div class="list-group list-group-flush">
                        @foreach($todayAppointments as $appointment)
                            <a href="{{ route('appointments.show', $appointment) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $appointment->patient->full_name }}</strong>
                                    <small>{{ \Carbon\Carbon::parse($appointment->slot_time)->format('g:i A') }}</small>
                                </div>
                                <small class="text-muted">
                                    {{ $appointment->doctor->name }} · {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 text-muted">No appointments scheduled for today.</div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection
