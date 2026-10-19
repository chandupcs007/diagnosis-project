@extends('layouts.app')

@section('title', 'Pharmacy GRN')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Pharmacy — GRN',
    'breadcrumb' => 'Pharmacy / GRN',
])
@endpush

@section('content')
@include('pharmacy.partials.nav')

<div class="card shadow-sm">
    <div class="card-header"><h6 class="m-0 font-weight-bold">Goods Receipt Note (GRN)</h6></div>
    <div class="card-body text-center py-5">
        <i class="fas fa-truck-loading fa-3x text-gray-300 mb-3"></i>
        <p class="text-muted mb-0">Record goods received against purchase orders here.</p>
    </div>
</div>
@endsection
