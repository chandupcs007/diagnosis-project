@extends('layouts.app')

@section('title', 'Authorization')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Authorization',
    'breadcrumb' => 'Laboratory / Authorization',
])
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@include('laboratory.partials.workflow-nav')

<div class="card shadow-sm sqa-list-card">
    <div class="card-header"><h6 class="m-0 font-weight-bold">Pending Authorization</h6></div>
    <div class="card-body">
        @include('laboratory.partials.filters', ['filterAction' => route('laboratory.authorization.index')])

        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 sqa-table">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Sample #</th>
                            <th>Patient</th>
                            <th>Test</th>
                            <th>Result</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td><strong>{{ $request->request_number }}</strong></td>
                                <td>{{ $request->sample_number }}</td>
                                <td>{{ $request->visit->patient->full_name }}</td>
                                <td>{{ $request->test_name }}</td>
                                <td>{{ $request->result_value }} {{ $request->unit }}</td>
                                <td><span class="badge bg-{{ $request->result_status === 'normal' ? 'success' : ($request->result_status === 'critical' ? 'danger' : 'warning') }}">{{ ucfirst($request->result_status) }}</span></td>
                                <td>
                                    <a href="{{ route('laboratory.authorization.show', $request) }}" class="btn btn-sm btn-primary">Review</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $requests->links() }}</div>
        @else
            <div class="text-center text-muted py-4">No reports pending authorization.</div>
        @endif
    </div>
</div>
@endsection
