@extends('layouts.app')

@section('title', 'Hospital Details')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Hospital Details',
    'breadcrumb' => 'Master / Hospital Details',
])
@endpush

@section('content')
@include('master.partials.nav')

<div class="sqa-form-card">
    <h2 class="sqa-section-header">Hospital Information</h2>
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

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('master.hospital.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label for="hospital_name">Hospital Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('hospital_name') is-invalid @enderror"
                               id="hospital_name" name="hospital_name"
                               value="{{ old('hospital_name', $hospital->hospital_name) }}" required>
                        @error('hospital_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="department_name">Department Name</label>
                        <input type="text" class="form-control @error('department_name') is-invalid @enderror"
                               id="department_name" name="department_name"
                               value="{{ old('department_name', $hospital->department_name) }}">
                        @error('department_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="contact_phone">Hospital Phone Number</label>
                        <input type="text" class="form-control @error('contact_phone') is-invalid @enderror"
                               id="contact_phone" name="contact_phone"
                               value="{{ old('contact_phone', $hospital->contact_phone) }}"
                               placeholder="+91 80 2206 5000">
                        @error('contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="contact_email">Hospital Email Address</label>
                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror"
                               id="contact_email" name="contact_email"
                               value="{{ old('contact_email', $hospital->contact_email) }}"
                               placeholder="info@hospital.com">
                        @error('contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="website">Hospital Website</label>
                        <input type="text" class="form-control @error('website') is-invalid @enderror"
                               id="website" name="website"
                               value="{{ old('website', $hospital->website) }}"
                               placeholder="www.hospital.com">
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="logo">Hospital Logo</label>
                        <input type="file" class="form-control @error('logo') is-invalid @enderror"
                               id="logo" name="logo" accept="image/*">
                        <small class="text-muted">Accepted formats: JPG, PNG, WEBP, SVG. Max size: 2 MB.</small>
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group mb-3">
                        <label for="address">Hospital Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="3"
                                  placeholder="Street, city, state, postal code">{{ old('address', $hospital->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group mb-3">
                        <label for="return_message">ID Card Return Message</label>
                        <input type="text" class="form-control @error('return_message') is-invalid @enderror"
                               id="return_message" name="return_message"
                               value="{{ old('return_message', $hospital->return_message) }}">
                        @error('return_message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            @php
                $logoUrl = $hospital->logo_path ? '/storage/' . ltrim($hospital->logo_path, '/') : null;
            @endphp

            @if($logoUrl)
                <div class="mb-4">
                    <label class="form-label d-block">Current Logo</label>
                    <img src="{{ $logoUrl }}" alt="Hospital Logo" class="img-thumbnail" style="max-height: 120px;">
                </div>
            @endif

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Hospital Details
                </button>
                <a href="{{ route('master.index') }}" class="btn btn-secondary">Back to Master</a>
            </div>
        </form>
    </div>
</div>
@endsection
