@extends('layouts.app')

@section('title', $section === 'current' ? 'Current Patients' : 'Previous Patients')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => $section === 'current' ? 'Current Patients' : 'Previous Patients',
    'breadcrumb' => 'Billing / ' . ($section === 'current' ? 'Current Patients' : 'Previous Patients'),
    'actions' => '<a href="' . route('billing.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left fa-sm me-1"></i> Back to Billing</a>',
])
@endpush

@section('content')
<div class="card shadow-sm mb-4 sqa-list-card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">{{ $section === 'current' ? 'Current Patients' : 'Previous Patients' }}</h6>
        @if($visits->count() > 0)
            <span class="sqa-list-count">{{ $visits->count() }} {{ Str::plural('patient', $visits->count()) }}</span>
        @endif
    </div>

    <div class="sqa-list-toolbar">
        <form method="GET" action="{{ route('billing.patients', $section) }}" class="sqa-search-form">
            <div class="sqa-search-input-group">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search by patient ID, name, or visit number..."
                       value="{{ $search }}"
                       aria-label="Search patients">
                <button class="btn btn-primary" type="submit" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
        @if($search !== '')
            <a href="{{ route('billing.patients', $section) }}" class="sqa-clear-search">
                <i class="fas fa-times me-1"></i> Clear
            </a>
        @endif
    </div>

    <div class="card-body p-0">
        @if($visits->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover sqa-data-table mb-0">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Visit Number</th>
                            <th>Patient Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Patient Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visits as $visit)
                            @php
                                $patient = $visit->patient;
                                $genderIcon = $patient->gender === 'male' ? 'mars' : ($patient->gender === 'female' ? 'venus' : 'genderless');
                            @endphp
                            <tr class="sqa-clickable-row"
                                tabindex="0"
                                data-href="{{ route('billing.show', ['visit' => $visit, 'from' => $section]) }}"
                                title="Open billing for {{ $patient->full_name }}">
                                <td><span class="sqa-patient-id">{{ $patient->display_id }}</span></td>
                                <td><strong>{{ $visit->visit_number }}</strong></td>
                                <td><span class="sqa-patient-name">{{ $patient->full_name }}</span></td>
                                <td>
                                    <span class="sqa-gender sqa-gender-{{ $patient->gender }}" title="{{ ucfirst($patient->gender) }}">
                                        <i class="fas fa-{{ $genderIcon }}" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ ucfirst($patient->gender) }}</span>
                                    </span>
                                </td>
                                <td>{{ $patient->date_of_birth ? $patient->date_of_birth->age . ' yrs' : '—' }}</td>
                                <td>
                                    <span class="badge {{ $visit->payer_type === 'insurance' ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $visit->payer_type_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                @if($search !== '')
                    No patients match “{{ $search }}”.
                @else
                    No {{ $section === 'current' ? 'current' : 'previous' }} patients to bill.
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.sqa-clickable-row').forEach(function (row) {
        row.addEventListener('click', function () {
            window.location = row.dataset.href;
        });
        row.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                window.location = row.dataset.href;
            }
        });
    });
});
</script>
@endpush
