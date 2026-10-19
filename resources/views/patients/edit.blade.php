@extends('layouts.app')

@section('title', 'Edit Patient')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Edit Patient',
    'breadcrumb' => 'Edit Patient',
])
@endpush

@section('content')
<div class="d-sm-flex justify-content-end mb-2">
    <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left fa-sm me-1"></i> Back to Details
    </a>
</div>

<div class="sqa-form-card">
    <h2 class="sqa-section-header">Patient Information</h2>
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

        <form method="POST" action="{{ route('patients.update', $patient->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                               id="first_name" name="first_name"
                               value="{{ old('first_name', $patient->first_name) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                               id="last_name" name="last_name"
                               value="{{ old('last_name', $patient->last_name) }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth <span class="required">*</span></label>
                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                               id="date_of_birth" name="date_of_birth"
                               value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}" required>
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender <span class="required">*</span></label>
                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row align-items-start g-3">
                <div class="col-lg-9">
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="blood_group">Blood Group</label>
                        <select class="form-control @error('blood_group') is-invalid @enderror" id="blood_group" name="blood_group">
                            <option value="">Select Blood Group</option>
                            @foreach(config('diagnosis.blood_groups') as $group)
                                <option value="{{ $group }}" {{ old('blood_group', $patient->blood_group) == $group ? 'selected' : '' }}>{{ $group }}</option>
                            @endforeach
                        </select>
                        @error('blood_group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone"
                               value="{{ old('phone', $patient->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email"
                               value="{{ old('email', $patient->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                               id="city" name="city"
                               value="{{ old('city', $patient->city) }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" class="form-control @error('state') is-invalid @enderror"
                               id="state" name="state"
                               value="{{ old('state', $patient->state) }}">
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="zip_code">ZIP Code</label>
                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror"
                               id="zip_code" name="zip_code"
                               value="{{ old('zip_code', $patient->zip_code) }}">
                        @error('zip_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="emergency_contact_name">Emergency Contact Name</label>
                        <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror"
                               id="emergency_contact_name" name="emergency_contact_name"
                               value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}">
                        @error('emergency_contact_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="form-group">
                        <label for="emergency_contact_phone">Emergency Contact Phone</label>
                        <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                               id="emergency_contact_phone" name="emergency_contact_phone"
                               value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}">
                        @error('emergency_contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="2">{{ old('address', $patient->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="form-group">
                        <label for="medical_history">Medical History</label>
                        <textarea class="form-control @error('medical_history') is-invalid @enderror"
                                  id="medical_history" name="medical_history" rows="2"
                                  placeholder="Pre-existing conditions, allergies, medications">{{ old('medical_history', $patient->medical_history) }}</textarea>
                        @error('medical_history')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Patient
                </button>
                <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-secondary ms-2">
                    Cancel
                </a>
            </div>
                </div>
                <div class="col-lg-3">
                    @include('patients.partials.biometrics', [
                        'layout' => 'sidebar',
                        'patient' => $patient,
                        'photoUrl' => $photoUrl ?? null,
                    ])
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
