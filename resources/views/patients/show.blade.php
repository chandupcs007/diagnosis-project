@extends('layouts.app')

@section('title', 'Patient Details')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Patient Details',
    'breadcrumb' => 'Patient',
    'actions' => '
        <a href="' . route('patients.id-card', $patient->id) . '" class="btn btn-info btn-sm text-white me-1" target="_blank" rel="noopener">
            <i class="fas fa-id-card fa-sm me-1"></i> Print ID Card
        </a>
        <a href="' . route('patients.edit', $patient->id) . '" class="btn btn-primary btn-sm me-1">
            <i class="fas fa-edit fa-sm me-1"></i> Edit Patient
        </a>
        <a href="' . route('patients.index') . '" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm me-1"></i> Back to Patients
        </a>
    ',
])
@endpush

@section('content')
@php
    $photoUrl = $photoUrl ?? $patient->photo_url;
@endphp

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <a href="{{ route('patients.id-card', $patient) }}" class="alert-link ms-2" target="_blank" rel="noopener">
            <i class="fas fa-id-card me-1"></i> Print Patient ID Card
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="sqa-patient-profile card shadow-sm mb-3">
    <div class="card-body">
        <div class="sqa-patient-profile-top">
            <div class="sqa-patient-profile-photo-wrap{{ $photoUrl ? ' has-photo' : '' }}" id="patientProfileAvatar">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $patient->full_name }}"
                         onerror="this.remove(); document.getElementById('patientProfileAvatar').classList.remove('has-photo'); document.getElementById('patientProfileAvatar').insertAdjacentHTML('afterbegin', '<div class=&quot;sqa-patient-profile-photo-empty&quot;><i class=&quot;fas fa-user&quot;></i></div>');">
                @else
                    <div class="sqa-patient-profile-photo-empty">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
            </div>

            <div class="sqa-patient-profile-identity">
                <h2 class="sqa-patient-profile-name">{{ $patient->full_name }}</h2>
                <div class="sqa-patient-profile-meta">
                    <span class="sqa-patient-profile-id">{{ $patient->display_id }}</span>
                    <span class="sqa-meta-sep">·</span>
                    <span>{{ $patient->age }} yrs</span>
                    <span class="sqa-meta-sep">·</span>
                    <span>{{ ucfirst($patient->gender) }}</span>
                    @if($patient->hasFingerprint())
                        <span class="badge bg-success ms-1"><i class="fas fa-fingerprint me-1"></i>Registered</span>
                    @else
                        <span class="badge bg-secondary ms-1"><i class="fas fa-fingerprint me-1"></i>No fingerprint</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="sqa-patient-profile-grid">
            <div class="sqa-patient-info-item">
                <span class="sqa-patient-info-label">Date of Birth</span>
                <span class="sqa-patient-info-value">{{ $patient->date_of_birth->format('M d, Y') }}</span>
            </div>
            <div class="sqa-patient-info-item">
                <span class="sqa-patient-info-label">Phone</span>
                <span class="sqa-patient-info-value">{{ $patient->phone ?: '—' }}</span>
            </div>
            <div class="sqa-patient-info-item">
                <span class="sqa-patient-info-label">Email</span>
                <span class="sqa-patient-info-value">{{ $patient->email ?: '—' }}</span>
            </div>
            <div class="sqa-patient-info-item">
                <span class="sqa-patient-info-label">Emergency Contact</span>
                <span class="sqa-patient-info-value">
                    @if($patient->emergency_contact_name)
                        {{ $patient->emergency_contact_name }}
                        @if($patient->emergency_contact_phone)
                            <span class="text-muted"> · {{ $patient->emergency_contact_phone }}</span>
                        @endif
                    @else
                        —
                    @endif
                </span>
            </div>
            <div class="sqa-patient-info-item">
                <span class="sqa-patient-info-label">Registered</span>
                <span class="sqa-patient-info-value">{{ $patient->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="sqa-patient-info-item">
                <span class="sqa-patient-info-label">Last Updated</span>
                <span class="sqa-patient-info-value">{{ $patient->updated_at->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        @if($patient->address || $patient->city || $patient->state || $patient->zip_code)
            <div class="sqa-patient-profile-section">
                <h6 class="sqa-patient-section-title"><i class="fas fa-map-marker-alt me-2"></i>Address</h6>
                <p class="sqa-patient-section-text mb-0">
                    @if($patient->address){{ $patient->address }}@endif
                    @if($patient->city || $patient->state || $patient->zip_code)
                        <br>{{ trim(collect([$patient->city, $patient->state])->filter()->implode(', ') . ' ' . $patient->zip_code) }}
                    @endif
                </p>
            </div>
        @endif

        @if($patient->medical_history)
            <div class="sqa-patient-profile-section">
                <h6 class="sqa-patient-section-title"><i class="fas fa-notes-medical me-2"></i>Medical History</h6>
                <p class="sqa-patient-section-text mb-0">{{ $patient->medical_history }}</p>
            </div>
        @endif
    </div>
</div>

<div class="sqa-patient-actions card shadow-sm">
    <div class="card-body py-2 px-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($patient->hasVisit() && $patient->latestVisit)
                <a href="{{ route('visits.show', $patient->latestVisit->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-file-medical me-1"></i> View Visit
                </a>
            @elseif(!$patient->hasVisit())
                <a href="{{ route('visits.create', $patient->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Create Visit
                </a>
            @endif
            <a href="{{ route('patients.id-card', $patient) }}" class="btn btn-info btn-sm text-white" target="_blank" rel="noopener">
                <i class="fas fa-id-card me-1"></i> Print ID Card
            </a>
            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="d-inline ms-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this patient? This action cannot be undone.')">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
