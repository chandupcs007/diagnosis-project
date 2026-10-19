@extends('layouts.app')

@section('title', 'Appointments')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Appointments',
    'breadcrumb' => 'Appointments',
    'actions' => '
        <a href="' . route('appointments.create') . '" class="btn btn-primary btn-sm me-2"><i class="fas fa-calendar-plus fa-sm me-1"></i> Book Appointment</a>
        <a href="' . route('appointments.check-in') . '" class="btn btn-success btn-sm"><i class="fas fa-id-card fa-sm me-1"></i> Patient Check-In</a>
    ',
])
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm mb-4 sqa-list-card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Appointment List</h6>
    </div>

    <div class="sqa-list-toolbar">
        <form method="GET" action="{{ route('appointments.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-0">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-0">Status</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">All</option>
                    @foreach(['scheduled', 'checked_in', 'completed', 'cancelled', 'no_show'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i> Filter</button>
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        @if($appointments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 sqa-table">
                    <thead>
                        <tr>
                            <th>Ref #</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                            <tr>
                                <td><strong>{{ $appointment->appointment_number }}</strong></td>
                                <td>{{ $appointment->appointment_date->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($appointment->slot_time)->format('g:i A') }}</td>
                                <td>
                                    {{ $appointment->patient->full_name }}
                                    <br>
                                    <small class="text-muted">{{ $appointment->patient->display_id }}</small>
                                </td>
                                <td>{{ $appointment->doctor->name }}</td>
                                <td>{{ $appointment->department }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($appointment->status) {
                                            'scheduled' => 'bg-primary',
                                            'checked_in' => 'bg-success',
                                            'completed' => 'bg-info',
                                            'cancelled' => 'bg-secondary',
                                            'no_show' => 'bg-warning text-dark',
                                            default => 'bg-light text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $appointments->links() }}</div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                <p>No appointments found.</p>
                <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">Book First Appointment</a>
            </div>
        @endif
    </div>
</div>
@endsection
