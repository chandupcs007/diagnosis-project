@extends('layouts.app')

@section('title', 'Report Details')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Report Data',
    'breadcrumb' => 'Laboratory / Report Data',
    'actions' => '<a href="' . route('laboratory.report-data.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to List</a>',
])
@endpush

@section('content')
<div class="row">
    <div class="col-lg-10">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Final Report</h6>
                <div>
                    <a href="{{ route('laboratory.report-data.print', $labRequest) }}" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                        <i class="fas fa-print me-1"></i> Print
                    </a>
                    <a href="{{ route('laboratory.report-data.index') }}" class="btn btn-secondary btn-sm ms-1">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @include('laboratory.partials.report-content', ['labRequest' => $labRequest])
            </div>
        </div>
    </div>
</div>
@endsection
