@extends('layouts.app')

@section('title', 'Assay to HIS Code Mapping')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Assay Code to HIS Code Mapping',
    'breadcrumb' => 'Master / LIS Mapping / Assay to HIS',
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
                <h6 class="font-weight-bold text-gray-800 mb-1">Assay Code to HIS Code Mapping</h6>
                <p class="text-muted small mb-0">Select analyzer and assay code, then map HIS lab tests (<strong>lab_test</strong>) stored in <strong>analyzer_assaycode_to_his</strong>.</p>
            </div>
            <a href="{{ route('master.lis-mapping.index') }}" class="btn btn-sm btn-outline-secondary">Back to LIS Mapping</a>
        </div>
        @include('master.lis-mapping.partials.mapping-options')
    </div>
</div>

@if($analyzers->isEmpty())
    <div class="alert alert-warning mb-0">
        No analyzers with assay codes yet. Complete
        <a href="{{ route('master.lis-mapping.analyzers') }}">Analyzer Mapping</a> and
        <a href="{{ route('master.lis-mapping.analyzer-assay-codes') }}">Analyzer Assay Code Mapping</a> first.
    </div>
@else
    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm sqa-form-card">
                <h2 class="sqa-section-header">Map to HIS Tests</h2>
                <div class="sqa-form-body">
                    <form method="POST" action="{{ route('master.lis-mapping.assay-his-codes.store') }}" id="assay-his-form">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="his_analyzer_id">Analyzer <span class="text-danger">*</span></label>
                            <select class="form-control" id="his_analyzer_id" name="analyzer_id" required>
                                <option value="">Select analyzer...</option>
                                @foreach($analyzers as $analyzer)
                                    <option value="{{ $analyzer->analyzer_id }}" {{ old('analyzer_id') === $analyzer->analyzer_id ? 'selected' : '' }}>
                                        {{ $analyzer->analyzer_name }} ({{ $analyzer->analyzer_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="analyzer_assaycode_id">Mapped Assay Code <span class="text-danger">*</span></label>
                            <select class="form-control" id="analyzer_assaycode_id" name="analyzer_assaycode_id" required disabled>
                                <option value="">Select analyzer first...</option>
                            </select>
                        </div>

                        <label class="form-label">HIS lab tests <span class="text-danger">*</span></label>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm mb-2" id="his_lab_test_search"
                                       placeholder="Search lab tests..." autocomplete="off" disabled>
                                <div id="his_lab_available_list" class="lis-test-pane" role="listbox"></div>
                                <small class="text-muted d-block mt-1">Double-click to add.</small>
                            </div>
                            <div class="col-md-6">
                                <div id="his_lab_selected_list" class="lis-test-pane" role="listbox"></div>
                                <small class="text-muted d-block mt-1">Double-click to remove.</small>
                                <div id="his_test_hidden_fields"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info text-white" id="assay-his-save-btn" disabled>
                            <i class="fas fa-save me-1"></i> Save HIS Mapping
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header"><h6 class="m-0 font-weight-bold">Registered Analyzers</h6></div>
                <div class="card-body p-0">
                    @if($registeredAnalyzers->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Analyzer Name</th>
                                        <th>Analyzer ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registeredAnalyzers as $reg)
                                        <tr class="his-filter-analyzer-row" data-analyzer-id="{{ $reg->analyzer_id }}">
                                            <td>
                                                <button type="button" class="btn btn-link p-0 text-start his-analyzer-filter-btn"
                                                        data-analyzer-id="{{ $reg->analyzer_id }}"
                                                        data-analyzer-name="{{ $reg->analyzer_name }}">
                                                    {{ $reg->analyzer_name }}
                                                </button>
                                            </td>
                                            <td><strong>{{ $reg->analyzer_id }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-2 border-top">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="his_mappings_show_all">Show all mappings</button>
                        </div>
                    @else
                        <div class="p-3 text-muted small">No analyzers registered.</div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm sqa-list-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Saved Assay → HIS Mappings <span id="his_mappings_filter_label" class="text-muted small"></span></h6>
                    <span class="sqa-list-count"><span id="his_mappings_visible_count">{{ $hisMappings->count() }}</span> shown</span>
                </div>
                <div class="card-body p-0">
                    @if($hisMappings->count())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 sqa-table">
                                <thead>
                                    <tr>
                                        <th>Analyzer</th>
                                        <th>Assay Code</th>
                                        <th>HIS Test Code</th>
                                        <th>Test Name</th>
                                    </tr>
                                </thead>
                                <tbody id="his_mappings_tbody">
                                    @foreach($hisMappings as $mapping)
                                        <tr data-analyzer-id="{{ $mapping->analyzer_id }}">
                                            <td>
                                                {{ $mapping->analyzer?->analyzer_name ?? $mapping->analyzer_id }}<br>
                                                <span class="small text-muted">{{ $mapping->analyzer_id }}</span>
                                            </td>
                                            <td>
                                                {{ $mapping->analyzerAssaycode?->assay_code ?? '—' }}<br>
                                                <span class="small text-muted">ID: {{ $mapping->analyzer_assaycode_id }}</span>
                                            </td>
                                            <td><strong>{{ $mapping->his_test_code }}</strong></td>
                                            <td>{{ $mapping->labTest?->name ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">No assay to HIS mappings yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    .lis-test-pane {
        height: 220px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background: #fff;
        font-size: 0.875rem;
    }
    .lis-test-pane .lis-test-item {
        padding: 0.45rem 0.65rem;
        cursor: pointer;
        border-bottom: 1px solid #f1f3f5;
        user-select: none;
    }
    .lis-test-pane .lis-test-item:hover { background: #e7f1ff; }
    .lis-test-pane .lis-test-empty {
        padding: 0.75rem;
        color: #6c757d;
        font-style: italic;
    }
    .lis-test-pane .code { font-weight: 600; color: #0d6efd; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const analyzerSelect = document.getElementById('his_analyzer_id');
    const assaySelect = document.getElementById('analyzer_assaycode_id');
    const searchInput = document.getElementById('his_lab_test_search');
    const availableList = document.getElementById('his_lab_available_list');
    const selectedList = document.getElementById('his_lab_selected_list');
    const hiddenFields = document.getElementById('his_test_hidden_fields');
    const saveBtn = document.getElementById('assay-his-save-btn');
    const form = document.getElementById('assay-his-form');

    if (!analyzerSelect) {
        return;
    }

    const allLabTests = @json($labTests ?? collect());
    const labDeptNames = @json(collect($departments ?? [])->mapWithKeys(fn ($d, $k) => [$k => $d['name'] ?? $k]));
    const assaysUrl = @json(route('master.lis-mapping.assay-his-codes.analyzer-assays'));
    const oldAssayId = @json(old('analyzer_assaycode_id'));
    const oldHisCodes = @json(old('his_test_codes', []));

    const labTestsById = Object.fromEntries(allLabTests.map(t => [t.id, t]));
    let selectedTests = [];

    function escapeHtml(v) {
        return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function selectedIds() {
        return selectedTests.map(t => t.id);
    }

    function syncHiddenFields() {
        hiddenFields.innerHTML = '';
        selectedTests.forEach(function (test) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'his_test_codes[]';
            input.value = test.id;
            hiddenFields.appendChild(input);
        });
        saveBtn.disabled = !(analyzerSelect.value && assaySelect.value && selectedTests.length);
    }

    function renderAvailable() {
        const q = (searchInput.value || '').trim().toLowerCase();
        const picked = new Set(selectedIds());
        availableList.innerHTML = '';

        const rows = allLabTests.filter(function (test) {
            if (picked.has(test.id)) return false;
            if (!q) return true;
            const hay = (test.id + ' ' + test.name + ' ' + (labDeptNames[test.group_id] || '')).toLowerCase();
            return hay.includes(q);
        });

        if (!rows.length) {
            availableList.innerHTML = '<div class="lis-test-empty">No tests match.</div>';
            return;
        }

        rows.forEach(function (test) {
            const row = document.createElement('div');
            row.className = 'lis-test-item';
            row.innerHTML = '<span class="code">' + escapeHtml(test.id) + '</span> — ' + escapeHtml(test.name);
            row.addEventListener('dblclick', function () { addTest(test.id); });
            availableList.appendChild(row);
        });
    }

    function renderSelected() {
        selectedList.innerHTML = '';
        if (!selectedTests.length) {
            selectedList.innerHTML = '<div class="lis-test-empty">No HIS tests selected.</div>';
            syncHiddenFields();
            return;
        }
        selectedTests.forEach(function (test) {
            const row = document.createElement('div');
            row.className = 'lis-test-item';
            row.innerHTML = '<span class="code">' + escapeHtml(test.id) + '</span> — ' + escapeHtml(test.name);
            row.addEventListener('dblclick', function () { removeTest(test.id); });
            selectedList.appendChild(row);
        });
        syncHiddenFields();
    }

    function addTest(id) {
        if (!labTestsById[id] || selectedIds().includes(id)) return;
        selectedTests.push(labTestsById[id]);
        renderAvailable();
        renderSelected();
    }

    function removeTest(id) {
        selectedTests = selectedTests.filter(t => t.id !== id);
        renderAvailable();
        renderSelected();
    }

    function setLabPickerEnabled(enabled) {
        searchInput.disabled = !enabled;
        if (!enabled) {
            selectedTests = [];
            renderAvailable();
            renderSelected();
        }
    }

    async function loadAssays(analyzerId) {
        assaySelect.innerHTML = '<option value="">Loading...</option>';
        assaySelect.disabled = true;
        setLabPickerEnabled(false);

        if (!analyzerId) {
            assaySelect.innerHTML = '<option value="">Select analyzer first...</option>';
            return;
        }

        const url = new URL(assaysUrl, window.location.origin);
        url.searchParams.set('analyzer_id', analyzerId);
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await response.json();
        const assays = data.assays || [];

        assaySelect.innerHTML = '<option value="">Select assay code...</option>';
        assays.forEach(function (assay) {
            const opt = document.createElement('option');
            opt.value = assay.id;
            opt.textContent = assay.assay_code + ' (ID: ' + assay.id + ')';
            if (String(oldAssayId) === String(assay.id)) {
                opt.selected = true;
            }
            assaySelect.appendChild(opt);
        });
        assaySelect.disabled = !assays.length;
        if (!assays.length) {
            assaySelect.innerHTML = '<option value="">No assay codes for this analyzer</option>';
        } else if (assaySelect.value) {
            setLabPickerEnabled(true);
            restoreOldSelection();
        }
        syncHiddenFields();
    }

    function restoreOldSelection() {
        selectedTests = [];
        oldHisCodes.forEach(function (id) {
            if (labTestsById[id]) selectedTests.push(labTestsById[id]);
        });
        renderAvailable();
        renderSelected();
    }

    function filterHisMappings(analyzerId, analyzerName) {
        const mappingsTbody = document.getElementById('his_mappings_tbody');
        const mappingsCountEl = document.getElementById('his_mappings_visible_count');
        const mappingsFilterLabel = document.getElementById('his_mappings_filter_label');

        if (!mappingsTbody) {
            return;
        }

        let visible = 0;
        mappingsTbody.querySelectorAll('tr[data-analyzer-id]').forEach(function (row) {
            const show = !analyzerId || row.dataset.analyzerId === analyzerId;
            row.classList.toggle('d-none', !show);
            if (show) {
                visible++;
            }
        });

        if (mappingsCountEl) {
            mappingsCountEl.textContent = visible;
        }
        if (mappingsFilterLabel) {
            mappingsFilterLabel.textContent = analyzerId
                ? '— ' + analyzerName + ' (' + analyzerId + ')'
                : '';
        }

        document.querySelectorAll('.his-filter-analyzer-row').forEach(function (row) {
            row.classList.toggle('table-active', analyzerId && row.dataset.analyzerId === analyzerId);
        });
    }

    analyzerSelect.addEventListener('change', function () {
        const opt = analyzerSelect.options[analyzerSelect.selectedIndex];
        const name = opt && analyzerSelect.value ? opt.textContent.split(' (')[0] : '';
        filterHisMappings(analyzerSelect.value, name);
        loadAssays(analyzerSelect.value).catch(function () {
            alert('Could not load assay codes.');
        });
    });

    assaySelect.addEventListener('change', function () {
        const on = !!assaySelect.value;
        setLabPickerEnabled(on);
        if (on) {
            renderAvailable();
            renderSelected();
        }
        syncHiddenFields();
    });

    searchInput.addEventListener('input', renderAvailable);

    form.addEventListener('submit', function (e) {
        syncHiddenFields();
        if (!selectedTests.length) {
            e.preventDefault();
            alert('Add at least one HIS lab test (double-click from the left list).');
        }
    });

    if (analyzerSelect.value) {
        loadAssays(analyzerSelect.value);
    }

    document.querySelectorAll('.his-analyzer-filter-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (analyzerSelect) {
                analyzerSelect.value = btn.dataset.analyzerId;
            }
            filterHisMappings(btn.dataset.analyzerId, btn.dataset.analyzerName);
            loadAssays(btn.dataset.analyzerId).catch(function () {
                alert('Could not load assay codes.');
            });
        });
    });

    const showAllBtn = document.getElementById('his_mappings_show_all');
    if (showAllBtn) {
        showAllBtn.addEventListener('click', function () {
            if (analyzerSelect) {
                analyzerSelect.value = '';
            }
            filterHisMappings('', '');
        });
    }
});
</script>
@endpush
