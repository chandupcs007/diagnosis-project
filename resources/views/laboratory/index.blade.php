@extends('layouts.app')

@section('title', 'Laboratory')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Laboratory',
    'breadcrumb' => 'Laboratory',
])
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@include('laboratory.partials.workflow-nav', ['stageCounts' => $stageCounts])

<div class="row mb-4">
    @foreach(config('diagnosis.lab_workflow_stages') as $key => $stage)
        <div class="col-md-4 col-lg mb-3">
            <a href="{{ route($stage['route']) }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-left-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ $stage['label'] }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stageCounts[$key] ?? 0 }} pending
                                </div>
                            </div>
                            <i class="{{ $stage['icon'] }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card shadow-sm sqa-list-card">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold">Recent Lab Requests</h6>
    </div>
    <div class="card-body p-0">
        @if($recentRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 sqa-table">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Patient</th>
                            <th>Test</th>
                            <th>Department</th>
                            <th>Sample #</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRequests as $request)
                            <tr>
                                <td><strong>{{ $request->request_number }}</strong></td>
                                <td>
                                    {{ $request->visit->patient->full_name }}
                                    <br><small class="text-muted">{{ $request->visit->patient->display_id ?? '—' }}</small>
                                </td>
                                <td>{{ $request->test_name }}</td>
                                <td>{{ $request->department_label }}</td>
                                <td>{{ $request->sample_number ?? '—' }}</td>
                                <td><span class="badge bg-info">{{ $request->status_label }}</span></td>
                                <td>
                                    <a href="{{ route($request->workflow_show_route_name, $request) }}"
                                       class="btn btn-sm btn-outline-primary">Open</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                No lab requests yet. Bill a lab test from a visit to create a request.
            </div>
        @endif
    </div>
</div>
@endsection
