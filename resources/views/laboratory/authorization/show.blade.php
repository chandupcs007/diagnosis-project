@extends('layouts.app')

@section('title', 'Authorize Report')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Authorization',
    'breadcrumb' => 'Laboratory / Authorization',
    'actions' => '<a href="' . route('laboratory.authorization.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to List</a>',
])
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold">Report for Authorization</h6></div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><strong>Request #:</strong> {{ $labRequest->request_number }}</div>
                    <div class="col-md-6"><strong>Sample #:</strong> {{ $labRequest->sample_number }}</div>
                    <div class="col-md-6"><strong>Patient:</strong> {{ $labRequest->visit->patient->full_name }}</div>
                    <div class="col-md-6"><strong>Test:</strong> {{ $labRequest->test_name }}</div>
                    <div class="col-md-6"><strong>Department:</strong> {{ $labRequest->department_label }}</div>
                    <div class="col-md-6"><strong>Reported By:</strong> {{ $labRequest->reportedByUser?->name ?? '—' }}</div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <tr>
                            <th>Result Value</th>
                            <td>{{ $labRequest->result_value }}</td>
                        </tr>
                        <tr>
                            <th>Unit</th>
                            <td>{{ $labRequest->unit ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Reference Range</th>
                            <td>{{ $labRequest->reference_range ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Result Status</th>
                            <td><span class="badge bg-{{ $labRequest->result_status === 'normal' ? 'success' : ($labRequest->result_status === 'critical' ? 'danger' : 'warning') }}">{{ ucfirst($labRequest->result_status) }}</span></td>
                        </tr>
                        <tr>
                            <th>Report Notes</th>
                            <td>{{ $labRequest->report_notes ?? '—' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('laboratory.authorization.approve', $labRequest) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Authorization Notes</label>
                                <textarea name="authorization_notes" class="form-control" rows="2">{{ old('authorization_notes') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-1"></i> Authorize & Publish
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('laboratory.authorization.reject', $labRequest) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" class="form-control" rows="2" required>{{ old('rejection_reason') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-1"></i> Reject & Send to Report Entry
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
