@extends('layouts.app')

@section('title', 'Sample Acknowledgement')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Sample Acknowledgement',
    'breadcrumb' => 'Laboratory / Sample Acknowledgement',
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
    <div class="card-header"><h6 class="m-0 font-weight-bold">Pending Sample Acknowledgement</h6></div>
    <div class="card-body">
        @include('laboratory.partials.filters', ['filterAction' => route('laboratory.acknowledgement.index')])

        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 sqa-table">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Test</th>
                            <th>Department</th>
                            <th>Sample Type</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td><strong>{{ $request->request_number }}</strong></td>
                                <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    {{ $request->visit->patient->full_name }}
                                    <br><small class="text-muted">{{ $request->visit->patient->display_id ?? '—' }}</small>
                                </td>
                                <td>{{ $request->test_name }}</td>
                                <td>{{ $request->department_label }}</td>
                                <td>{{ $request->sample_type }}</td>
                                <td>
                                    <a href="{{ route('laboratory.acknowledgement.show', $request) }}" class="btn btn-sm btn-primary">
                                        Acknowledge
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $requests->links() }}</div>
        @else
            <div class="text-center text-muted py-4">No requests pending acknowledgement.</div>
        @endif
    </div>
</div>
@endsection
