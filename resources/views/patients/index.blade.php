@extends('layouts.app')

@section('title', 'Patients')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Patient Management',
    'breadcrumb' => 'Patients',
    'actions' => '<a href="' . route('patients.create') . '" class="btn btn-primary btn-sm"><i class="fas fa-user-plus fa-sm me-1"></i> Register New Patient</a>',
])
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show auto-dismiss-alert mb-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" onclick="closeAlert(this)"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show auto-dismiss-alert mb-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" onclick="closeAlert(this)"></button>
    </div>
@endif

<div class="card shadow-sm mb-4 sqa-list-card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Patient List</h6>
        @if($patients->count() > 0)
            <span class="sqa-list-count">{{ $patients->count() }} {{ Str::plural('record', $patients->count()) }}</span>
        @endif
    </div>

    <div class="sqa-list-toolbar">
        <form method="GET" action="{{ route('patients.index') }}" class="sqa-search-form">
            <div class="sqa-search-input-group">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search by ID, first name, or last name..."
                       value="{{ $search ?? '' }}"
                       aria-label="Search patients">
                <button class="btn btn-primary" type="submit" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <div class="sqa-fingerprint-search-wrap">
            <button type="button" class="btn btn-outline-primary btn-sm" id="fingerprint-search-btn" title="Scan fingerprint to open patient">
                <i class="fas fa-fingerprint me-1"></i> Scan Fingerprint
            </button>
            <span class="sqa-fingerprint-search-status text-muted" id="fingerprint-search-status"></span>
        </div>

        @if(isset($search) && $search)
            <a href="{{ route('patients.index') }}" class="sqa-clear-search">
                <i class="fas fa-times me-1"></i> Clear
            </a>
        @endif
    </div>

    <div id="fingerprint-search-alert" class="alert alert-warning py-2 px-3 mb-0 d-none sqa-fingerprint-search-banner" role="alert"></div>

    @if(isset($search) && $search && $patients->count() > 0)
        @php
            $patientsWithVisits = $patients->filter(fn ($p) => $p->hasVisit())->count();
        @endphp
        <div class="sqa-search-banner">
            <i class="fas fa-search me-2"></i>
            Results for <strong>&ldquo;{{ $search }}&rdquo;</strong>
            @if($patientsWithVisits > 0)
                <span class="sqa-search-visit-hint">
                    <i class="fas fa-calendar-check ms-2 me-1"></i>
                    {{ $patientsWithVisits }} {{ Str::plural('patient', $patientsWithVisits) }} with existing {{ Str::plural('visit', $patientsWithVisits) }} highlighted in green
                </span>
            @endif
        </div>
    @endif

    <div class="card-body p-0">
        @if($patients->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover sqa-data-table mb-0" id="patientsTable">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-name">Name</th>
                            <th class="col-dob">Date of Birth</th>
                            <th class="col-age">Age</th>
                            <th class="col-gender">Gender</th>
                            <th class="col-phone">Phone</th>
                            <th class="col-email">Email</th>
                            <th class="col-actions text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                        <tr class="{{ $patient->hasVisit() ? 'sqa-row-has-visit' : '' }}">
                            <td class="col-id">
                                <span class="sqa-patient-id">{{ $patient->display_id }}</span>
                                @if($patient->isTemp())
                                    <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Temp</span>
                                @endif
                            </td>
                            <td class="col-name">
                                <div class="sqa-name-cell">
                                    <span class="sqa-patient-name">{{ trim($patient->first_name . ' ' . $patient->last_name) ?: '—' }}</span>
                                    @if($patient->hasVisit() && $patient->latestVisit)
                                        <a href="{{ route('visits.show', $patient->latestVisit->id) }}"
                                           class="sqa-visit-badge"
                                           title="View visit {{ $patient->latestVisit->visit_number }}">
                                            <i class="fas fa-calendar-check"></i>
                                            {{ $patient->latestVisit->visit_number }}
                                        </a>
                                    @elseif($patient->hasVisit())
                                        <span class="sqa-visit-badge sqa-visit-badge-static" title="Visit already created">
                                            <i class="fas fa-calendar-check"></i> Visit exists
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="col-dob">{{ $patient->date_of_birth->format('M d, Y') }}</td>
                            <td class="col-age">{{ $patient->date_of_birth->age }} yrs</td>
                            <td class="col-gender">
                                <span class="sqa-gender sqa-gender-{{ $patient->gender }}">
                                    <i class="fas fa-{{ $patient->gender == 'male' ? 'mars' : ($patient->gender == 'female' ? 'venus' : 'genderless') }}"></i>
                                    {{ ucfirst($patient->gender) }}
                                </span>
                            </td>
                            <td class="col-phone">
                                @if($patient->phone)
                                    {{ $patient->phone }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="col-email">
                                @if($patient->email)
                                    <span class="sqa-email" title="{{ $patient->email }}">{{ $patient->email }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="col-actions text-end">
                                <div class="sqa-row-actions">
                                    <a href="{{ route('patients.show', $patient->id) }}"
                                       class="sqa-action-btn"
                                       title="View"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($patient->hasVisit() && $patient->latestVisit)
                                        <a href="{{ route('visits.show', $patient->latestVisit->id) }}"
                                           class="sqa-action-btn sqa-action-visit-details"
                                           title="View Visit Details ({{ $patient->latestVisit->visit_number }})"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-file-medical"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('visits.create', $patient->id) }}"
                                           class="sqa-action-btn sqa-action-visit"
                                           title="Generate Visit"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-calendar-plus"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('patients.edit', $patient->id) }}"
                                       class="sqa-action-btn sqa-action-edit"
                                       title="Edit"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @unless($patient->hasVisit())
                                        <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="sqa-action-btn sqa-action-delete"
                                                    title="Delete"
                                                    data-bs-toggle="tooltip"
                                                    onclick="return confirm('Delete patient #{{ $patient->id }}? This cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @if(isset($search) && $search)
                <div class="sqa-empty-state">
                    <i class="fas fa-search"></i>
                    <h4>No patients found</h4>
                    <p>No match for &ldquo;{{ $search }}&rdquo;. Try a different search or register a new patient.</p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary btn-sm">Clear Search</a>
                        <a href="{{ route('patients.create') }}?search={{ urlencode($search) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus me-1"></i> Register Patient
                        </a>
                    </div>
                </div>
            @else
                <div class="sqa-empty-state">
                    <i class="fas fa-users"></i>
                    <h4>No patients yet</h4>
                    <p>Register your first patient to get started.</p>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus me-1"></i> Register First Patient
                    </a>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
@php
    $biometricService = app(\App\Services\PatientBiometricService::class);
    $simulationPatients = config('diagnosis.fingerprint_simulation')
        ? $patients->filter(fn ($p) => $p->hasFingerprint())->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->full_name,
            'pid' => $p->display_id,
            'simulation_template' => $biometricService->buildSimulationTemplate($p),
        ])->values()
        : collect();
@endphp
<script>
    window.SqaFingerprintSearch = {
        searchUrl: @json(route('patients.search-fingerprint')),
        fingerprintBridgeUrl: @json(config('diagnosis.fingerprint_bridge_url')),
        fingerprintBridgeTimeout: @json(config('diagnosis.fingerprint_bridge_timeout')),
        fingerprintSimulation: @json((bool) config('diagnosis.fingerprint_simulation')),
        simulationPatients: @json($simulationPatients),
    };
</script>
<script src="{{ asset('js/patient-fingerprint-search.js') }}"></script>
<script>
    function closeAlert(button) {
        const alert = button.closest('.alert');
        if (alert) {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.auto-dismiss-alert').forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });

        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
            new bootstrap.Tooltip(el);
        });

        const searchInput = document.querySelector('.sqa-search-form input[name="search"]');
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
