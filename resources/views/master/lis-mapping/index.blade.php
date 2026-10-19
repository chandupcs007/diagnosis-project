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
        <p class="text-muted mb-0">
            Align laboratory catalog items (<strong>lab_test</strong>) with LIS or analyzer codes so orders and results sync correctly between the billing catalog and the lab workflow.
        </p>
    </div>
</div>

<div class="card shadow-sm sqa-list-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">Laboratory Catalog</h6>
        <span class="sqa-list-count">{{ $legacyTests->count() }} tests shown</span>
    </div>
    <div class="card-body p-0">
        @if($legacyTests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 sqa-table">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Department</th>
                            <th>Test Name</th>
                            <th>LIS / Instrument Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($legacyTests as $test)
                            <tr>
                                <td><strong>{{ $test->id }}</strong></td>
                                <td>{{ $departments[$test->group_id]['name'] ?? $test->group_id }}</td>
                                <td>{{ $test->name }}</td>
                                <td class="text-muted">—</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                No laboratory catalog entries yet. Create laboratory services under
                <a href="{{ route('master.services.create') }}">Create Service</a> first.
            </div>
        @endif
    </div>
</div>
@endsection
