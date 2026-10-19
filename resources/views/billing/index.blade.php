@extends('layouts.app')

@section('title', 'Billing')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Billing',
    'breadcrumb' => 'Billing',
])
@endpush

@section('content')
<div class="sqa-billing-sections">
    <a href="{{ route('billing.patients', 'current') }}" class="sqa-billing-section">
        <span class="sqa-billing-section-icon"><i class="fas fa-user-clock"></i></span>
        <span class="sqa-billing-section-copy">
            <span class="sqa-billing-section-label">Current Patients</span>
            <span class="sqa-billing-section-hint">Open visits</span>
        </span>
        <span class="sqa-billing-section-count">{{ $currentCount }}</span>
    </a>
    <a href="{{ route('billing.patients', 'previous') }}" class="sqa-billing-section">
        <span class="sqa-billing-section-icon"><i class="fas fa-history"></i></span>
        <span class="sqa-billing-section-copy">
            <span class="sqa-billing-section-label">Previous Patients</span>
            <span class="sqa-billing-section-hint">Completed visits</span>
        </span>
        <span class="sqa-billing-section-count">{{ $previousCount }}</span>
    </a>
</div>
@endsection
