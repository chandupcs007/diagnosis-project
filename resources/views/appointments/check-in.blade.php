@extends('layouts.app')

@section('title', 'Patient Check-In')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Patient Check-In',
    'breadcrumb' => 'Patient Check-In',
])
@endpush

@section('content')
<div class="d-sm-flex justify-content-end mb-2">
    <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left fa-sm me-1"></i> Back to Appointments
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-xl-6">
        <div class="sqa-form-card">
            <h2 class="sqa-section-header">Convert Temp Number to PID</h2>
            <div class="sqa-form-body">
                <div class="alert alert-info">
                    <i class="fas fa-id-card me-1"></i>
                    When a new patient arrives with their <strong>Temp Number</strong>, enter it below.
                    The system will generate a permanent <strong>PID</strong> and link it to their patient record.
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('appointments.check-in.store') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="temp_number">Temp Number <span class="required">*</span></label>
                        <input type="text" class="form-control @error('temp_number') is-invalid @enderror"
                               id="temp_number" name="temp_number"
                               value="{{ old('temp_number') }}"
                               placeholder="e.g. TEMP202607260001" required>
                        @error('temp_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-check me-1"></i> Check In & Generate PID
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
