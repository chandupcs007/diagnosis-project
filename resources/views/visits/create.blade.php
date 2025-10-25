@extends('layouts.app')

@section('title', 'Create Visit')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Create New Visit</h1>
    <a href="{{ route('patients.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Patients
    </a>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Visit Information</h6>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Patient Information Card -->
                <div class="card mb-4 border-left-primary">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="fas fa-user-injured me-2"></i>Patient Information
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Name:</strong> {{ $patient->first_name }} {{ $patient->last_name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Patient ID:</strong> #{{ $patient->id }}
                            </div>
                            <div class="col-md-4">
                                <strong>Age:</strong> {{ $patient->date_of_birth->age }} years
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('visits.store') }}">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctor_id">Select Doctor *</label>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department">Department *</label>
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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="visit_date">Visit Date *</label>
                                <input type="date" class="form-control @error('visit_date') is-invalid @enderror" 
                                       id="visit_date" name="visit_date" 
                                       value="{{ old('visit_date', date('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('visit_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="visit_time">Visit Time *</label>
                                <input type="time" class="form-control @error('visit_time') is-invalid @enderror" 
                                       id="visit_time" name="visit_time" 
                                       value="{{ old('visit_time', '09:00') }}" required>
                                @error('visit_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="symptoms">Symptoms / Chief Complaints</label>
                        <textarea class="form-control @error('symptoms') is-invalid @enderror" 
                                  id="symptoms" name="symptoms" rows="3" 
                                  placeholder="Describe patient symptoms...">{{ old('symptoms') }}</textarea>
                        @error('symptoms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-check me-2"></i>Create Visit
                        </button>
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum time to current time if today is selected
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