@extends('layouts.app')

@section('title', 'Sample Receive')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Sample Receive',
    'breadcrumb' => 'Laboratory / Sample Receive',
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
    <div class="card-header"><h6 class="m-0 font-weight-bold">Pending Sample Receive</h6></div>
    <div class="card-body">
        @include('laboratory.partials.filters', ['filterAction' => route('laboratory.receive.index')])

        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 sqa-table">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Sample #</th>
                            <th>Patient</th>
                            <th>Test</th>
                            <th>Department</th>
                            <th>Acknowledged</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td><strong>{{ $request->request_number }}</strong></td>
                                <td><strong class="text-primary">{{ $request->sample_number }}</strong></td>
                                <td>{{ $request->visit->patient->full_name }}</td>
                                <td>{{ $request->test_name }}</td>
                                <td>{{ $request->department_label }}</td>
                                <td>{{ $request->acknowledged_at?->format('d M Y H:i') ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('laboratory.receive.show', $request) }}" class="btn btn-sm btn-primary">Receive</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $requests->links() }}</div>
        @else
            <div class="text-center text-muted py-4">No samples pending receive.</div>
        @endif
    </div>
</div>
@endsection
