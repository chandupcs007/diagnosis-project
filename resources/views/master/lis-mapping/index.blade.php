@extends('layouts.app')

@section('title', 'LIS Mapping')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'LIS Mapping',
    'breadcrumb' => 'Master / LIS Mapping',
])
@endpush

@section('content')
@include('master.partials.nav')

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h6 class="font-weight-bold text-gray-800 mb-2">Laboratory Information System Mapping</h6>
        <p class="text-muted mb-3">
            Align laboratory catalog items (<strong>lab_test</strong>) with LIS or analyzer codes so orders and results sync correctly between the billing catalog and the lab workflow.
        </p>
        @include('master.lis-mapping.partials.mapping-options')
    </div>
</div>
@endsection
