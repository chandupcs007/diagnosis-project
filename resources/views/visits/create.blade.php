@extends('layouts.app')

@section('title', 'Create Visit')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Create Visit',
    'breadcrumb' => 'Create Visit',
])
@endpush

@section('content')
<div class="d-sm-flex justify-content-end mb-2">
    <a href="{{ route('patients.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left fa-sm me-1"></i> Back to Patients
    </a>
</div>

<div class="sqa-form-card mb-4">
    <h2 class="sqa-section-header">Patient Information</h2>
    <div class="sqa-form-body py-3">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-2">
                <small class="text-uppercase text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.6px;">Name</small>
                <span>{{ $patient->first_name }} {{ $patient->last_name }}</span>
            </div>
            <div class="col-xl-3 col-md-6 mb-2">
                <small class="text-uppercase text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.6px;">Patient ID</small>
                <span>#{{ $patient->id }}</span>
            </div>
            <div class="col-xl-3 col-md-6 mb-2">
                <small class="text-uppercase text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.6px;">Age</small>
                <span>{{ $patient->date_of_birth->age }} years</span>
            </div>
        </div>
    </div>
</div>

<div class="sqa-form-card">
    <h2 class="sqa-section-header">Visit Information</h2>
    <div class="sqa-form-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('visits.store') }}">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="doctor_id">Select Doctor <span class="required">*</span></label>
                        <select class="form-control @error('doctor_id') is-invalid @enderror"
                                id="doctor_id" name="doctor_id" required>
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->name }}
                                    @if($doctor->specialization)
                                        - {{ $doctor->specialization }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="department">Department <span class="required">*</span></label>
                        <select class="form-control @error('department') is-invalid @enderror"
                                id="department" name="department" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}" {{ old('department') == $department ? 'selected' : '' }}>
                                    {{ $department }}
                                </option>
                            @endforeach
                        </select>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="visit_date">Visit Date <span class="required">*</span></label>
                        <input type="date" class="form-control @error('visit_date') is-invalid @enderror"
                               id="visit_date" name="visit_date"
                               value="{{ old('visit_date', date('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}" required>
                        @error('visit_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="visit_time">Visit Time <span class="required">*</span></label>
                        <input type="time" class="form-control @error('visit_time') is-invalid @enderror"
                               id="visit_time" name="visit_time"
                               value="{{ old('visit_time', '09:00') }}" required>
                        @error('visit_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="payer_type">Patient Type <span class="required">*</span></label>
                        <select class="form-control @error('payer_type') is-invalid @enderror"
                                id="payer_type" name="payer_type" required>
                            @foreach(config('diagnosis.payer_types') as $value => $label)
                                <option value="{{ $value }}" {{ old('payer_type', 'self_pay') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('payer_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="form-group">
                        <label for="symptoms">Symptoms / Chief Complaints</label>
                        <textarea class="form-control @error('symptoms') is-invalid @enderror"
                                  id="symptoms" name="symptoms" rows="2"
                                  placeholder="Describe patient symptoms...">{{ old('symptoms') }}</textarea>
                        @error('symptoms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-check me-1"></i> Create Visit
                </button>
                <a href="{{ route('patients.index') }}" class="btn btn-secondary ms-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const visitDate = document.getElementById('visit_date');
        const visitTime = document.getElementById('visit_time');

        visitDate.addEventListener('change', function() {
            const today = new Date().toISOString().split('T')[0];
            if (this.value === today) {
                const now = new Date();
                const currentTime = now.getHours().toString().padStart(2, '0') + ':' +
                                  now.getMinutes().toString().padStart(2, '0');
                visitTime.min = currentTime;
            } else {
                visitTime.removeAttribute('min');
            }
        });
    });
</script>
@endpush
