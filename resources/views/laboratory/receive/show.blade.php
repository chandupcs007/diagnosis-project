@extends('layouts.app')

@section('title', 'Receive Sample')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Sample Receive',
    'breadcrumb' => 'Laboratory / Receive',
    'actions' => '<a href="' . route('laboratory.receive.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to List</a>',
])
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="m-0 font-weight-bold">Sample Details</h6></div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><strong>Request #:</strong> {{ $labRequest->request_number }}</div>
                    <div class="col-md-6"><strong>Sample #:</strong> <span class="text-primary">{{ $labRequest->sample_number }}</span></div>
                    <div class="col-md-6"><strong>Patient:</strong> {{ $labRequest->visit->patient->full_name }}</div>
                    <div class="col-md-6"><strong>Test:</strong> {{ $labRequest->test_name }}</div>
                    <div class="col-md-6"><strong>Department:</strong> {{ $labRequest->department_label }}</div>
                    <div class="col-md-6"><strong>Sample Type:</strong> {{ $labRequest->sample_type }}</div>
                    <div class="col-md-6"><strong>Acknowledged By:</strong> {{ $labRequest->acknowledgedByUser?->name ?? '—' }}</div>
                    <div class="col-md-6"><strong>Acknowledged At:</strong> {{ $labRequest->acknowledged_at?->format('d M Y H:i') ?? '—' }}</div>
                </div>

                <form method="POST" action="{{ route('laboratory.receive.store', $labRequest) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Receive Notes</label>
                        <textarea name="receive_notes" class="form-control" rows="3"
                                  placeholder="Sample condition, storage notes...">{{ old('receive_notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-vial me-1"></i> Confirm Sample Receive
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
