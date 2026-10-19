@extends('layouts.app')

@section('title', 'Create Service')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Create Service',
    'breadcrumb' => 'Master / Create Service',
])
@endpush

@push('styles')
<style>
    .profile-test-pane {
        height: 220px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background: #fff;
        font-size: 0.875rem;
        line-height: 1.35;
    }
    .profile-test-pane .profile-test-item {
        padding: 0.45rem 0.65rem;
        cursor: pointer;
        border-bottom: 1px solid #f1f3f5;
        user-select: none;
    }
    .profile-test-pane .profile-test-item:hover,
    .profile-test-pane .profile-test-item:focus {
        background: #e7f1ff;
        outline: none;
    }
    .profile-test-pane .profile-test-item .code {
        font-weight: 600;
        color: #0d6efd;
    }
    .profile-test-pane .profile-test-empty {
        padding: 0.75rem;
        color: #6c757d;
        font-style: italic;
    }
</style>
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

<div class="sqa-form-card mb-4">
    <h2 class="sqa-section-header">Service Type</h2>
    <div class="sqa-form-body">
        <div class="row g-3 mb-2">
            <div class="col-md-6">
                <button type="button" id="mode-hospital" class="btn btn-outline-primary w-100 py-3 service-mode-btn">
                    <i class="fas fa-hospital me-2"></i> Hospital Service
                </button>
            </div>
            <div class="col-md-6">
                <button type="button" id="mode-laboratory" class="btn btn-outline-success w-100 py-3 service-mode-btn">
                    <i class="fas fa-flask me-2"></i> Laboratory
                </button>
            </div>
        </div>
        <p class="text-muted small mb-0">Choose a service type to load categories or laboratory departments and generate the next item code automatically.</p>
    </div>
</div>

<form method="POST" action="{{ route('master.services.store') }}" id="service-form" class="d-none">
    @csrf
    <input type="hidden" name="service_mode" id="service_mode" value="{{ old('service_mode') }}">

    <div class="sqa-form-card mb-4" id="hospital-panel" style="display: none;">
        <h2 class="sqa-section-header">Hospital Service</h2>
        <div class="sqa-form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="category_nr">Hospital Service Category <span class="text-danger">*</span></label>
                        <select class="form-control" id="category_nr" name="category_nr">
                            <option value="">Select category...</option>
                            @foreach($hospitalCategories as $category)
                                <option value="{{ $category->nr }}"
                                    data-category="{{ $category->category }}"
                                    {{ (string) old('category_nr') === (string) $category->nr ? 'selected' : '' }}>
                                    {{ $category->category }} — {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="hospital_item_code">Item Code</label>
                        <input type="text" class="form-control bg-light" id="hospital_item_code" readonly
                               placeholder="Select a category to generate code">
                        <small class="text-muted">Last code in category + 1 (e.g. 101-3 → 101-4)</small>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label for="hospital_service_name">Service Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="hospital_service_name" name="service_name"
                               value="{{ old('service_mode') === 'hospital' ? old('service_name') : '' }}"
                               maxlength="250" placeholder="Enter service name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="hospital_unit_price">Unit Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="hospital_unit_price"
                               name="unit_price"
                               value="{{ old('service_mode') === 'hospital' ? old('unit_price') : '' }}"
                               placeholder="0.00">
                    </div>
                </div>
            </div>
            <p class="text-muted small mb-0">Saved to <strong>hospital_service</strong> and <strong>billing_master</strong>.</p>
        </div>
    </div>

    <div class="sqa-form-card mb-4" id="laboratory-panel" style="display: none;">
        <h2 class="sqa-section-header">Laboratory Service</h2>
        <div class="sqa-form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="department">Laboratory Department <span class="text-danger">*</span></label>
                        <select class="form-control" id="department" name="department">
                            <option value="">Select department...</option>
                            @foreach($labDepartments as $key => $department)
                                <option value="{{ $key }}" {{ old('department') === $key ? 'selected' : '' }}>
                                    {{ $department['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="lab_item_code">Item Code</label>
                        <input type="text" class="form-control bg-light" id="lab_item_code" readonly
                               placeholder="Select department to generate code">
                        <small class="text-muted">Last lab_test id in department + 1 (e.g. _bio005 → _bio006)</small>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label for="lab_service_name">Service / Test Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lab_service_name"
                               maxlength="250" placeholder="e.g. Lipid Profile"
                               value="{{ old('service_mode') === 'laboratory' ? old('service_name') : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="lab_unit_price">Unit Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="lab_unit_price"
                               placeholder="0.00"
                               value="{{ old('service_mode') === 'laboratory' ? old('unit_price') : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="is_profile">Is Profile?</label>
                        <select class="form-control" id="is_profile">
                            <option value="0" {{ (string) old('is_profile', '0') === '0' ? 'selected' : '' }}>No (single test)</option>
                            <option value="1" {{ (string) old('is_profile') === '1' ? 'selected' : '' }}>Yes (profile / panel)</option>
                        </select>
                        <small class="text-muted">Default 0 in <strong>lab_test_param</strong>.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="sample_type">Sample Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="sample_type">
                            @foreach(config('diagnosis.sample_types', []) as $sampleTypeOption)
                                <option value="{{ $sampleTypeOption }}" @selected(old('sample_type', 'Serum') === $sampleTypeOption)>
                                    {{ $sampleTypeOption }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Saved in <strong>lab_test.sample</strong>.</small>
                    </div>
                </div>
                <div class="col-12" id="profile-tests-panel" style="display: none;">
                    <div class="form-group mb-3">
                        <label>Profile Tests <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="profile_test_search" class="small text-muted mb-1">Available tests (from lab_test)</label>
                                <input type="text" class="form-control form-control-sm mb-2" id="profile_test_search"
                                       placeholder="Search by item code or description..." autocomplete="off">
                                <div id="profile_available_list" class="profile-test-pane" role="listbox" aria-label="Available lab tests"></div>
                                <small class="text-muted d-block mt-1">Double-click a test to add it to the profile.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">Selected for this profile</label>
                                <div id="profile_selected_list" class="profile-test-pane" role="listbox" aria-label="Selected profile tests"></div>
                                <small class="text-muted d-block mt-1">Double-click to remove. Item code and description are saved in <strong>lab_test.child</strong>.</small>
                                <div id="profile_child_hidden_fields"></div>
                            </div>
                        </div>
                        @error('profile_child_codes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <p class="text-muted small mb-0">Profile save creates one <strong>lab_test</strong> row (with <strong>child</strong>), one <strong>billing_master</strong> row, plus <strong>lab_test_param</strong> / <strong>lab_test_profile_child</strong>.</p>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4" id="form-actions" style="display: none;">
        <button type="submit" class="btn btn-primary" id="save-service-btn">
            <i class="fas fa-save me-1"></i> Save Service
        </button>
        <a href="{{ route('master.index') }}" class="btn btn-secondary">Back to Master</a>
    </div>
</form>

@include('master.services.partials.catalog-manager')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('service-form');
    const modeInput = document.getElementById('service_mode');
    const hospitalPanel = document.getElementById('hospital-panel');
    const laboratoryPanel = document.getElementById('laboratory-panel');
    const formActions = document.getElementById('form-actions');
    const hospitalBtn = document.getElementById('mode-hospital');
    const laboratoryBtn = document.getElementById('mode-laboratory');
    const categorySelect = document.getElementById('category_nr');
    const departmentSelect = document.getElementById('department');
    const hospitalItemCode = document.getElementById('hospital_item_code');
    const labItemCode = document.getElementById('lab_item_code');
    const hospitalServiceName = document.getElementById('hospital_service_name');
    const labServiceName = document.getElementById('lab_service_name');
    const hospitalUnitPrice = document.getElementById('hospital_unit_price');
    const labUnitPrice = document.getElementById('lab_unit_price');

    const hospitalCodeUrl = @json(route('master.services.next-hospital-code'));
    const laboratoryCodeUrl = @json(route('master.services.next-laboratory-code'));
    const allLabTests = @json($selectableLabTests->values());
    const labDeptNames = @json(collect($labDepartments)->mapWithKeys(fn ($d, $k) => [$k => $d['name'] ?? $k]));
    const oldProfileChildren = @json(old('profile_child_codes', []));
    const isProfileSelect = document.getElementById('is_profile');
    const sampleTypeSelect = document.getElementById('sample_type');
    const profileTestsPanel = document.getElementById('profile-tests-panel');
    const profileSearchInput = document.getElementById('profile_test_search');
    const profileAvailableList = document.getElementById('profile_available_list');
    const profileSelectedList = document.getElementById('profile_selected_list');
    const profileHiddenFields = document.getElementById('profile_child_hidden_fields');
    const labTestsById = Object.fromEntries(allLabTests.map(function (test) {
        return [test.id, test];
    }));
    let selectedProfileTests = [];

    function setMode(mode) {
        modeInput.value = mode;
        form.classList.remove('d-none');
        formActions.style.display = 'flex';

        hospitalPanel.style.display = mode === 'hospital' ? 'block' : 'none';
        laboratoryPanel.style.display = mode === 'laboratory' ? 'block' : 'none';

        hospitalBtn.classList.toggle('btn-primary', mode === 'hospital');
        hospitalBtn.classList.toggle('btn-outline-primary', mode !== 'hospital');
        laboratoryBtn.classList.toggle('btn-success', mode === 'laboratory');
        laboratoryBtn.classList.toggle('btn-outline-success', mode !== 'laboratory');

        syncActiveFieldNames(mode);
    }

    function syncActiveFieldNames(mode) {
        if (mode === 'hospital') {
            hospitalServiceName.setAttribute('name', 'service_name');
            hospitalUnitPrice.setAttribute('name', 'unit_price');
            labServiceName.removeAttribute('name');
            labUnitPrice.removeAttribute('name');
            departmentSelect.removeAttribute('name');
            categorySelect.setAttribute('name', 'category_nr');
            isProfileSelect.removeAttribute('name');
            sampleTypeSelect.removeAttribute('name');
            profileHiddenFields.innerHTML = '';
        } else {
            labServiceName.setAttribute('name', 'service_name');
            labUnitPrice.setAttribute('name', 'unit_price');
            hospitalServiceName.removeAttribute('name');
            hospitalUnitPrice.removeAttribute('name');
            categorySelect.removeAttribute('name');
            departmentSelect.setAttribute('name', 'department');
            isProfileSelect.setAttribute('name', 'is_profile');
            sampleTypeSelect.setAttribute('name', 'sample_type');
            syncProfileHiddenFields();
        }
    }

    function selectedProfileIds() {
        return selectedProfileTests.map(function (test) {
            return test.id;
        });
    }

    function syncProfileHiddenFields() {
        profileHiddenFields.innerHTML = '';
        if (modeInput.value !== 'laboratory' || isProfileSelect.value !== '1') {
            return;
        }

        selectedProfileTests.forEach(function (test) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'profile_child_codes[]';
            input.value = test.id;
            profileHiddenFields.appendChild(input);
        });
    }

    function renderProfileAvailableList() {
        const query = (profileSearchInput.value || '').trim().toLowerCase();
        const selectedIds = new Set(selectedProfileIds());
        profileAvailableList.innerHTML = '';

        const matches = allLabTests.filter(function (test) {
            if (selectedIds.has(test.id)) {
                return false;
            }
            if (!query) {
                return true;
            }
            const haystack = (test.id + ' ' + test.name + ' ' + (labDeptNames[test.group_id] || '')).toLowerCase();
            return haystack.includes(query);
        });

        if (!matches.length) {
            profileAvailableList.innerHTML = '<div class="profile-test-empty">No tests match your search.</div>';
            return;
        }

        matches.forEach(function (test) {
            const row = document.createElement('div');
            row.className = 'profile-test-item';
            row.tabIndex = 0;
            row.dataset.testId = test.id;
            row.innerHTML = '<span class="code">' + escapeHtml(test.id) + '</span> — ' +
                escapeHtml(test.name) +
                ' <span class="text-muted">(' + escapeHtml(labDeptNames[test.group_id] || test.group_id) + ')</span>';

            row.addEventListener('dblclick', function () {
                addProfileTest(test.id);
            });
            row.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    addProfileTest(test.id);
                }
            });

            profileAvailableList.appendChild(row);
        });
    }

    function renderProfileSelectedList() {
        profileSelectedList.innerHTML = '';

        if (!selectedProfileTests.length) {
            profileSelectedList.innerHTML = '<div class="profile-test-empty">No tests selected yet.</div>';
            syncProfileHiddenFields();
            return;
        }

        selectedProfileTests.forEach(function (test) {
            const row = document.createElement('div');
            row.className = 'profile-test-item';
            row.tabIndex = 0;
            row.dataset.testId = test.id;
            row.innerHTML = '<span class="code">' + escapeHtml(test.id) + '</span> — ' + escapeHtml(test.name);

            row.addEventListener('dblclick', function () {
                removeProfileTest(test.id);
            });
            row.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === 'Delete' || event.key === 'Backspace') {
                    removeProfileTest(test.id);
                }
            });

            profileSelectedList.appendChild(row);
        });

        syncProfileHiddenFields();
    }

    function addProfileTest(testId) {
        if (selectedProfileIds().includes(testId) || !labTestsById[testId]) {
            return;
        }
        selectedProfileTests.push(labTestsById[testId]);
        renderProfileAvailableList();
        renderProfileSelectedList();
    }

    function removeProfileTest(testId) {
        selectedProfileTests = selectedProfileTests.filter(function (test) {
            return test.id !== testId;
        });
        renderProfileAvailableList();
        renderProfileSelectedList();
    }

    function restoreOldProfileSelection() {
        selectedProfileTests = [];
        oldProfileChildren.forEach(function (testId) {
            if (labTestsById[testId]) {
                selectedProfileTests.push(labTestsById[testId]);
            }
        });
        renderProfileAvailableList();
        renderProfileSelectedList();
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function toggleProfilePanel() {
        const isProfile = isProfileSelect.value === '1';
        profileTestsPanel.style.display = isProfile ? 'block' : 'none';
        if (isProfile) {
            if (!selectedProfileTests.length && oldProfileChildren.length) {
                restoreOldProfileSelection();
            } else {
                renderProfileAvailableList();
                renderProfileSelectedList();
            }
        } else {
            profileHiddenFields.innerHTML = '';
        }
    }

    async function fetchHospitalCode() {
        const categoryNr = categorySelect.value;
        hospitalItemCode.value = '';
        if (!categoryNr) {
            return;
        }

        const url = new URL(hospitalCodeUrl, window.location.origin);
        url.searchParams.set('category_nr', categoryNr);

        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) {
            return;
        }

        const data = await response.json();
        hospitalItemCode.value = data.item_code || '';
    }

    async function fetchLaboratoryCode() {
        const department = departmentSelect.value;
        labItemCode.value = '';
        if (!department) {
            return;
        }

        const url = new URL(laboratoryCodeUrl, window.location.origin);
        url.searchParams.set('department', department);

        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) {
            return;
        }

        const data = await response.json();
        labItemCode.value = data.item_code || '';
    }

    hospitalBtn.addEventListener('click', function () {
        setMode('hospital');
        fetchHospitalCode();
    });

    laboratoryBtn.addEventListener('click', function () {
        setMode('laboratory');
        fetchLaboratoryCode();
    });

    categorySelect.addEventListener('change', fetchHospitalCode);
    departmentSelect.addEventListener('change', fetchLaboratoryCode);
    isProfileSelect.addEventListener('change', toggleProfilePanel);
    profileSearchInput.addEventListener('input', renderProfileAvailableList);

    form.addEventListener('submit', function (event) {
        syncProfileHiddenFields();
        if (modeInput.value === 'laboratory' && isProfileSelect.value === '1' && !selectedProfileTests.length) {
            event.preventDefault();
            alert('Please add at least one test to the profile (double-click from the left list).');
        }
    });

    const oldMode = @json(old('service_mode'));
    if (oldMode === 'hospital' || oldMode === 'laboratory') {
        setMode(oldMode);
        if (oldMode === 'hospital') {
            fetchHospitalCode();
        } else {
            fetchLaboratoryCode();
            toggleProfilePanel();
        }
    }

    if (oldMode === 'laboratory') {
        toggleProfilePanel();
    }
});
</script>
@endpush
