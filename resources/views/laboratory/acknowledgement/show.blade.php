@extends('layouts.app')

@section('title', 'Acknowledge Sample')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Sample Acknowledgement',
    'breadcrumb' => 'Laboratory / Acknowledge',
    'actions' => '<a href="' . route('laboratory.acknowledgement.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to List</a>',
])
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold">Request Details</h6></div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><strong>Request #:</strong> {{ $labRequest->request_number }}</div>
                    <div class="col-md-6"><strong>Visit #:</strong> {{ $labRequest->visit->visit_number }}</div>
                    <div class="col-md-6"><strong>Patient:</strong> {{ $labRequest->visit->patient->full_name }}</div>
                    <div class="col-md-6"><strong>Patient ID:</strong> {{ $labRequest->visit->patient->display_id ?? '—' }}</div>
                    <div class="col-md-6"><strong>Doctor:</strong> {{ $labRequest->visit->doctor->name }}</div>
                    <div class="col-md-6"><strong>Test:</strong> {{ $labRequest->test_name }}</div>
                    <div class="col-md-6"><strong>Department:</strong> {{ $labRequest->department_label }}</div>
                    <div class="col-md-6"><strong>Sample Type:</strong> {{ $labRequest->sample_type }}</div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    On acknowledgement, a sample number will be generated for
                    <strong>{{ $labRequest->department_label }}</strong> / <strong>{{ $labRequest->sample_type }}</strong>.
                </div>

                <form method="POST" action="{{ route('laboratory.acknowledgement.store', $labRequest) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Acknowledgement Notes</label>
                        <textarea name="acknowledgement_notes" class="form-control" rows="3"
                                  placeholder="Optional notes...">{{ old('acknowledgement_notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i> Acknowledge Sample
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
