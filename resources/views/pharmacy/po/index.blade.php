@extends('layouts.app')

@section('title', 'Pharmacy PO')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Pharmacy — Purchase Order',
    'breadcrumb' => 'Pharmacy / PO',
])
@endpush

@section('content')
@include('pharmacy.partials.nav')

<div class="card shadow-sm">
    <div class="card-header"><h6 class="m-0 font-weight-bold">Purchase Order (PO)</h6></div>
    <div class="card-body text-center py-5">
        <i class="fas fa-file-invoice fa-3x text-gray-300 mb-3"></i>
        <p class="text-muted mb-0">Raise and track pharmacy purchase orders here.</p>
    </div>
</div>
@endsection
