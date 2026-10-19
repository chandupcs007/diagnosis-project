@extends('layouts.app')

@section('title', 'Pharmacy Item Creation')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Pharmacy — Item Creation',
    'breadcrumb' => 'Pharmacy / Item Creation',
])
@endpush

@section('content')
@include('pharmacy.partials.nav')

<div class="card shadow-sm">
    <div class="card-header"><h6 class="m-0 font-weight-bold">Item Creation</h6></div>
    <div class="card-body text-center py-5">
        <i class="fas fa-pills fa-3x text-gray-300 mb-3"></i>
        <p class="text-muted mb-0">Create and maintain pharmacy inventory items here.</p>
    </div>
</div>
@endsection
