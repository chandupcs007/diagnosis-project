@extends('layouts.app')

@section('title', 'Enter Report')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Report Entry',
    'breadcrumb' => 'Laboratory / Report Entry',
    'actions' => '<a href="' . route('laboratory.report-entry.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to List</a>',
])
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">
        @if($labRequest->is_rejected)
            <div class="alert alert-warning">
                <strong>Rejected for correction:</strong> {{ $labRequest->rejection_reason }}
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold">Test Information</h6></div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><strong>Request #:</strong> {{ $labRequest->request_number }}</div>
                    <div class="col-md-6"><strong>Sample #:</strong> {{ $labRequest->sample_number }}</div>
                    <div class="col-md-6"><strong>Patient:</strong> {{ $labRequest->visit->patient->full_name }}</div>
                    <div class="col-md-6"><strong>Test:</strong> {{ $labRequest->test_name }}</div>
                    <div class="col-md-6"><strong>Department:</strong> {{ $labRequest->department_label }}</div>
                    <div class="col-md-6"><strong>Sample Type:</strong> {{ $labRequest->sample_type }}</div>
                </div>

                <form method="POST" action="{{ route('laboratory.report-entry.store', $labRequest) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Result Value <span class="text-danger">*</span></label>
                            <input type="text" name="result_value" class="form-control"
                                   value="{{ old('result_value', $labRequest->result_value) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control"
                                   value="{{ old('unit', $labRequest->unit ?? $labRequest->labTest?->unit) }}"
                                   placeholder="mg/dL, g/dL...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reference Range</label>
                            <input type="text" name="reference_range" class="form-control"
                                   value="{{ old('reference_range', $labRequest->reference_range ?? $labRequest->labTest?->reference_range) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Result Status <span class="text-danger">*</span></label>
                            <select name="result_status" class="form-control" required>
                                @foreach(['normal', 'abnormal', 'critical'] as $status)
                                    <option value="{{ $status }}"
                                        {{ old('result_status', $labRequest->result_status ?? 'normal') === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Report Notes</label>
                            <textarea name="report_notes" class="form-control" rows="3">{{ old('report_notes', $labRequest->report_notes) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Report & Send for Authorization
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
