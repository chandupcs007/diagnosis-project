@extends('layouts.app')

@section('title', 'Analyzer Mapping')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Analyzer Mapping',
    'breadcrumb' => 'Master / LIS Mapping / Analyzers',
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
                <h6 class="font-weight-bold text-gray-800 mb-1">Analyzer Mapping</h6>
                <p class="text-muted small mb-0">Enter analyzer name and analyzer ID. Data is stored in <strong>analyzer_details</strong>.</p>
            </div>
            <a href="{{ route('master.lis-mapping.index') }}" class="btn btn-sm btn-outline-secondary">Back to LIS Mapping</a>
        </div>
        @include('master.lis-mapping.partials.mapping-options')
    </div>
</div>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm sqa-form-card">
            <h2 class="sqa-section-header">Add Analyzer</h2>
            <div class="sqa-form-body">
                <form method="POST" action="{{ route('master.lis-mapping.analyzers.store') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="analyzer_name">Analyzer Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="analyzer_name" name="analyzer_name"
                               maxlength="150" placeholder="e.g. Cobas c311" value="{{ old('analyzer_name') }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="analyzer_id">Analyzer ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="analyzer_id" name="analyzer_id"
                               maxlength="50" placeholder="e.g. COBAS-C311-01" value="{{ old('analyzer_id') }}" required>
                        <small class="text-muted">Unique identifier used by LIS / instrument interface.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Analyzer
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm sqa-list-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Registered Analyzers</h6>
                <span class="sqa-list-count">{{ $analyzers->count() }} total</span>
            </div>
            <div class="card-body p-0">
                @if($analyzers->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 sqa-table">
                            <thead>
                                <tr>
                                    <th>Analyzer Name</th>
                                    <th>Analyzer ID</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyzers as $analyzer)
                                    <tr class="analyzer-pick-row" data-analyzer-id="{{ $analyzer->analyzer_id }}">
                                        <td>
                                            <button type="button" class="btn btn-link p-0 text-start analyzer-name-btn"
                                                    data-analyzer-id="{{ $analyzer->analyzer_id }}"
                                                    data-analyzer-name="{{ $analyzer->analyzer_name }}">
                                                {{ $analyzer->analyzer_name }}
                                            </button>
                                        </td>
                                        <td><strong>{{ $analyzer->analyzer_id }}</strong></td>
                                        <td class="text-muted small">{{ $analyzer->created_at?->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">No analyzers registered yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row d-none" id="analyzer-his-mappings-section">
    <div class="col-12 mb-4">
        <div class="card shadow-sm sqa-list-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Saved Assay → HIS Mappings — <span id="analyzer-his-mappings-title"></span></h6>
                <span class="sqa-list-count"><span id="analyzer-his-mappings-count">0</span> total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 sqa-table">
                        <thead>
                            <tr>
                                <th>Assay Code</th>
                                <th>Assay ID</th>
                                <th>HIS Test Code</th>
                                <th>Test Name</th>
                                <th>Mapped</th>
                            </tr>
                        </thead>
                        <tbody id="analyzer-his-mappings-body">
                            <tr><td colspan="5" class="text-muted p-3">Select an analyzer name above.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mappingsUrlTemplate = @json(url('/master/lis-mapping/analyzers')) + '/';
    const section = document.getElementById('analyzer-his-mappings-section');
    const titleEl = document.getElementById('analyzer-his-mappings-title');
    const countEl = document.getElementById('analyzer-his-mappings-count');
    const bodyEl = document.getElementById('analyzer-his-mappings-body');

    function escapeHtml(v) {
        return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    async function loadHisMappings(analyzerId, analyzerName) {
        document.querySelectorAll('.analyzer-pick-row').forEach(function (row) {
            row.classList.toggle('table-active', row.dataset.analyzerId === analyzerId);
        });

        titleEl.textContent = analyzerName + ' (' + analyzerId + ')';
        section.classList.remove('d-none');
        bodyEl.innerHTML = '<tr><td colspan="5" class="text-muted p-3">Loading...</td></tr>';

        const response = await fetch(mappingsUrlTemplate + encodeURIComponent(analyzerId) + '/his-mappings', {
            headers: { 'Accept': 'application/json' },
        });
        const data = await response.json();
        const rows = data.mappings || [];
        countEl.textContent = rows.length;

        if (!rows.length) {
            bodyEl.innerHTML = '<tr><td colspan="5" class="text-muted p-3">No assay → HIS mappings for this analyzer yet.</td></tr>';
            return;
        }

        bodyEl.innerHTML = '';
        rows.forEach(function (row) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td>' + escapeHtml(row.assay_code) + '</td>' +
                '<td>' + escapeHtml(row.analyzer_assaycode_id) + '</td>' +
                '<td><strong>' + escapeHtml(row.his_test_code) + '</strong></td>' +
                '<td>' + escapeHtml(row.test_name) + '</td>' +
                '<td class="text-muted small">' + escapeHtml(row.created_at) + '</td>';
            bodyEl.appendChild(tr);
        });

        section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    document.querySelectorAll('.analyzer-name-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            loadHisMappings(btn.dataset.analyzerId, btn.dataset.analyzerName).catch(function () {
                alert('Could not load HIS mappings for this analyzer.');
            });
        });
    });
});
</script>
@endpush
