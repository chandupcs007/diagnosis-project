@extends('layouts.app')

@section('title', 'Analyzer Assay Code Mapping')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Analyzer Assay Code Mapping',
    'breadcrumb' => 'Master / LIS Mapping / Analyzer Assay Codes',
])
@endpush

@section('content')
@include('master.partials.nav')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 class="font-weight-bold text-gray-800 mb-1">Analyzer Assay Code Mapping</h6>
                <p class="text-muted small mb-0">Select a registered analyzer and enter the instrument assay code. Saved in <strong>analyzer_assaycode_details</strong>.</p>
            </div>
            <a href="{{ route('master.lis-mapping.index') }}" class="btn btn-sm btn-outline-secondary">Back to LIS Mapping</a>
        </div>
        @include('master.lis-mapping.partials.mapping-options')
    </div>
</div>

@if($analyzers->isEmpty())
    <div class="alert alert-warning">
        No analyzers registered yet.
        <a href="{{ route('master.lis-mapping.analyzers') }}">Add analyzers</a> before mapping assay codes.
    </div>
@else
    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm sqa-form-card">
                <h2 class="sqa-section-header">Add Assay Code</h2>
                <div class="sqa-form-body">
                    <form method="POST" action="{{ route('master.lis-mapping.analyzer-assay-codes.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="analyzer_id">Analyzer <span class="text-danger">*</span></label>
                            <select class="form-control" id="analyzer_id" name="analyzer_id" required>
                                <option value="">Select analyzer...</option>
                                @foreach($analyzers as $analyzer)
                                    <option value="{{ $analyzer->analyzer_id }}"
                                        {{ old('analyzer_id') === $analyzer->analyzer_id ? 'selected' : '' }}>
                                        {{ $analyzer->analyzer_name }} ({{ $analyzer->analyzer_id }})
                                        — {{ $analyzer->assay_codes_count }} assay code(s)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="assay_code">Assay Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="assay_code" name="assay_code"
                                   maxlength="100" placeholder="e.g. GLU, 001234" value="{{ old('assay_code') }}" required>
                            <small class="text-muted">Code as defined on the analyzer / LIS interface.</small>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Save Assay Mapping
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header"><h6 class="m-0 font-weight-bold">Registered Analyzers</h6></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($analyzers as $analyzer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>{{ $analyzer->analyzer_name }}</strong><br>
                                    <span class="small text-muted">{{ $analyzer->analyzer_id }}</span>
                                </span>
                                <span class="badge bg-primary rounded-pill">{{ $analyzer->assay_codes_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm sqa-list-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Mapped Assay Codes</h6>
                    <span class="sqa-list-count">{{ $assayMappings->count() }} total</span>
                </div>
                <div class="card-body p-0">
                    @if($assayMappings->count())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 sqa-table">
                                <thead>
                                    <tr>
                                        <th>Analyzer Name</th>
                                        <th>Analyzer ID</th>
                                        <th>Assay Code</th>
                                        <th>Added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assayMappings as $mapping)
                                        <tr>
                                            <td>{{ $mapping->analyzer?->analyzer_name ?? '—' }}</td>
                                            <td><strong>{{ $mapping->analyzer_id }}</strong></td>
                                            <td>{{ $mapping->assay_code }}</td>
                                            <td class="text-muted small">{{ $mapping->created_at?->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">No assay codes mapped yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
