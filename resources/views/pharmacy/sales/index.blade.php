@extends('layouts.app')

@section('title', 'Pharmacy Sales')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Pharmacy — Sales',
    'breadcrumb' => 'Pharmacy / Sales',
])
@endpush

@section('content')
@include('pharmacy.partials.nav')

<div class="card shadow-sm">
    <div class="card-header"><h6 class="m-0 font-weight-bold">Sales</h6></div>
    <div class="card-body text-center py-5">
        <i class="fas fa-cash-register fa-3x text-gray-300 mb-3"></i>
        <p class="text-muted mb-0">Pharmacy sales screen — point-of-sale and dispensing will be implemented here.</p>
    </div>
</div>
@endsection
