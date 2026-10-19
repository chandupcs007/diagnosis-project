@extends('layouts.app')

@section('title', 'Visit Details')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Visit Details',
    'breadcrumb' => 'Visit',
    'actions' => '
        <a href="' . route('patients.show', $visit->patient_id) . '" class="btn btn-primary btn-sm me-1">
            <i class="fas fa-user fa-sm me-1"></i> Patient Profile
        </a>
        <a href="' . route('patients.index') . '" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm me-1"></i> Back to Patients
        </a>
    ',
])
@endpush

@section('content')
@php
    $patient = $visit->patient;
    $statusColors = [
        'scheduled' => 'warning',
        'in_progress' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];
    $tabs = [
        'vitals' => ['label' => 'Vitals', 'icon' => 'fa-heartbeat'],
        'diagnosis' => ['label' => 'Diagnosis', 'icon' => 'fa-diagnoses'],
        'prescription' => ['label' => 'Doctor Prescription', 'icon' => 'fa-prescription'],
        'chief-complaints' => ['label' => 'Chief Complaints', 'icon' => 'fa-stethoscope'],
        'history' => ['label' => 'History', 'icon' => 'fa-history'],
        'billing' => ['label' => 'Billing Services', 'icon' => 'fa-file-invoice-dollar'],
        'lab-results' => ['label' => 'Lab Results', 'icon' => 'fa-flask'],
        'radiology' => ['label' => 'Radiology Results', 'icon' => 'fa-x-ray'],
        'last-visits' => ['label' => 'Last Visits', 'icon' => 'fa-calendar-check'],
    ];
    $activeTab = array_key_exists($activeTab ?? 'vitals', $tabs) ? ($activeTab ?? 'vitals') : 'vitals';
@endphp

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Patient + Visit summary header --}}
<div class="sqa-visit-header card shadow-sm mb-3">
    <div class="card-body">
        <div class="sqa-visit-header-inner">
            <div class="sqa-visit-patient-block">
                <div class="sqa-visit-avatar{{ $patient->photo_url ? ' sqa-visit-avatar--photo' : '' }}" id="visitPatientAvatar">
                    @if($patient->photo_url)
                        <img src="{{ $patient->photo_url }}" alt="{{ $patient->full_name }}"
                             onerror="this.remove(); document.getElementById('visitPatientAvatar').classList.remove('sqa-visit-avatar--photo'); document.getElementById('visitPatientAvatar').insertAdjacentHTML('afterbegin', '<i class=&quot;fas fa-user&quot;></i>');">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </div>
                <div class="sqa-visit-patient-details">
                    <div class="sqa-visit-patient-name">{{ $patient->full_name }}</div>
                    <div class="sqa-visit-meta-inline">
                        <span>#{{ $patient->id }}</span>
                        <span class="sqa-meta-sep">·</span>
                        <span>{{ $patient->age }} yrs</span>
                        <span class="sqa-meta-sep">·</span>
                        <span>{{ ucfirst($patient->gender) }}</span>
                        @if($patient->phone)
                            <span class="sqa-meta-sep">·</span>
                            <span><i class="fas fa-phone fa-xs me-1"></i>{{ $patient->phone }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="sqa-visit-header-divider" aria-hidden="true"></div>

            <div class="sqa-visit-details-block">
                <div class="sqa-visit-detail">
                    <span class="sqa-visit-detail-label">Visit No.</span>
                    <span class="badge bg-primary">{{ $visit->visit_number }}</span>
                </div>
                <div class="sqa-visit-detail">
                    <span class="sqa-visit-detail-label">Date</span>
                    <span class="sqa-visit-detail-value">{{ $visit->visit_date->format('M d, Y') }}</span>
                </div>
                <div class="sqa-visit-detail">
                    <span class="sqa-visit-detail-label">Time</span>
                    <span class="sqa-visit-detail-value">{{ date('h:i A', strtotime($visit->visit_time)) }}</span>
                </div>
                <div class="sqa-visit-detail">
                    <span class="sqa-visit-detail-label">Department</span>
                    <span class="badge bg-info text-dark">{{ $visit->department }}</span>
                </div>
                <div class="sqa-visit-detail">
                    <span class="sqa-visit-detail-label">Status</span>
                    <span class="badge bg-{{ $statusColors[$visit->status] ?? 'secondary' }}">
                        {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                    </span>
                </div>
                <div class="sqa-visit-detail">
                    <span class="sqa-visit-detail-label">Doctor</span>
                    <span class="sqa-visit-detail-value">
                        @php
                            $doctorName = $visit->doctor->name;
                            $doctorDisplay = str_starts_with(strtolower($doctorName), 'dr') ? $doctorName : 'Dr. ' . $doctorName;
                        @endphp
                        {{ $doctorDisplay }}
                        @if($visit->doctor->specialization)
                            <span class="text-muted">({{ $visit->doctor->specialization }})</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        @include('visits.partials.header-vitals')
    </div>
</div>

{{-- Tabbed records --}}
<div class="sqa-visit-tabs card shadow-sm">
    <div class="sqa-visit-tab-nav">
        <ul class="nav nav-tabs border-0" id="visitTabs" role="tablist">
            @foreach($tabs as $key => $tab)
                <li class="nav-item" role="presentation">
                    <button class="nav-link sqa-visit-tab {{ $activeTab === $key ? 'active' : '' }}"
                            id="tab-{{ $key }}"
                            data-visit-tab="{{ $key }}"
                            type="button"
                            role="tab"
                            aria-selected="{{ $activeTab === $key ? 'true' : 'false' }}"
                            aria-controls="pane-{{ $key }}">
                        <i class="fas {{ $tab['icon'] }} me-1"></i>{{ $tab['label'] }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="card-body sqa-visit-tab-body">
        <div class="tab-content" id="visitTabContent">
            <div class="tab-pane fade {{ $activeTab === 'vitals' ? 'show active' : '' }}" id="pane-vitals" role="tabpanel" aria-labelledby="tab-vitals">
                @include('visits.partials.tabs.vitals')
            </div>
            <div class="tab-pane fade {{ $activeTab === 'diagnosis' ? 'show active' : '' }}" id="pane-diagnosis" role="tabpanel" aria-labelledby="tab-diagnosis">
                @include('visits.partials.tabs.diagnosis')
            </div>
            <div class="tab-pane fade {{ $activeTab === 'prescription' ? 'show active' : '' }}" id="pane-prescription" role="tabpanel" aria-labelledby="tab-prescription">
                @include('visits.partials.tabs.prescription')
            </div>
            <div class="tab-pane fade {{ $activeTab === 'chief-complaints' ? 'show active' : '' }}" id="pane-chief-complaints" role="tabpanel">
                @include('visits.partials.tabs.chief-complaints')
            </div>
            <div class="tab-pane fade {{ $activeTab === 'history' ? 'show active' : '' }}" id="pane-history" role="tabpanel">
                @include('visits.partials.tabs.history')
            </div>
            <div class="tab-pane fade {{ $activeTab === 'billing' ? 'show active' : '' }}" id="pane-billing" role="tabpanel">
                @include('visits.partials.tabs.billing')
            </div>
            <div class="tab-pane fade {{ $activeTab === 'lab-results' ? 'show active' : '' }}" id="pane-lab-results" role="tabpanel">
                @include('visits.partials.tabs.lab-results')
            </div>
            <div class="tab-pane fade {{ $activeTab === 'radiology' ? 'show active' : '' }}" id="pane-radiology" role="tabpanel">
                @include('visits.partials.tabs.radiology')
            </div>
            <div class="tab-pane fade {{ $activeTab === 'last-visits' ? 'show active' : '' }}" id="pane-last-visits" role="tabpanel" aria-labelledby="tab-last-visits">
                @include('visits.partials.tabs.last-visits')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('#visitTabs [data-visit-tab]');
        const tabPanes = document.querySelectorAll('#visitTabContent .tab-pane');

        function showVisitTab(tabId) {
            tabButtons.forEach(function (btn) {
                const isActive = btn.getAttribute('data-visit-tab') === tabId;
                btn.classList.toggle('active', isActive);
                btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            tabPanes.forEach(function (pane) {
                const isActive = pane.id === 'pane-' + tabId;
                pane.classList.toggle('show', isActive);
                pane.classList.toggle('active', isActive);
            });
        }

        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const tabId = this.getAttribute('data-visit-tab');
                showVisitTab(tabId);

                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                history.replaceState(null, '', url);
            });
        });

        showVisitTab(@json($activeTab));
    });
</script>
@endpush
