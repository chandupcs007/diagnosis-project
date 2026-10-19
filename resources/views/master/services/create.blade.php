@extends('layouts.app')

@section('title', 'Create Service')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Create Service',
    'breadcrumb' => 'Master / Create Service',
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
                               maxlength="250" placeholder="Enter test name"
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
            </div>
            <p class="text-muted small mb-0">Saved to <strong>lab_test</strong> and <strong>billing_master</strong>.</p>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4" id="form-actions" style="display: none;">
        <button type="submit" class="btn btn-primary" id="save-service-btn">
            <i class="fas fa-save me-1"></i> Save Service
        </button>
        <a href="{{ route('master.index') }}" class="btn btn-secondary">Back to Master</a>
    </div>
</form>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header"><h6 class="m-0 font-weight-bold">Recent Hospital Services</h6></div>
            <div class="card-body p-0">
                @if($recentHospitalServices->count())
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Code</th><th>Category</th><th>Description</th></tr></thead>
                            <tbody>
                                @foreach($recentHospitalServices as $service)
                                    <tr>
                                        <td><strong>{{ $service->code }}</strong></td>
                                        <td>{{ $service->category }}</td>
                                        <td>{{ $service->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-3 text-muted">No hospital services yet.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header"><h6 class="m-0 font-weight-bold">Recent Laboratory Services</h6></div>
            <div class="card-body p-0">
                @if($recentLabServices->count())
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Code</th><th>Department</th><th>Name</th></tr></thead>
                            <tbody>
                                @foreach($recentLabServices as $test)
                                    <tr>
                                        <td><strong>{{ $test->id }}</strong></td>
                                        <td>{{ config('diagnosis.lab_departments.' . $test->group_id . '.name', $test->group_id) }}</td>
                                        <td>{{ $test->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-3 text-muted">No laboratory services yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>
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
        } else {
            labServiceName.setAttribute('name', 'service_name');
            labUnitPrice.setAttribute('name', 'unit_price');
            hospitalServiceName.removeAttribute('name');
            hospitalUnitPrice.removeAttribute('name');
            categorySelect.removeAttribute('name');
            departmentSelect.setAttribute('name', 'department');
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

    const oldMode = @json(old('service_mode'));
    if (oldMode === 'hospital' || oldMode === 'laboratory') {
        setMode(oldMode);
        if (oldMode === 'hospital') {
            fetchHospitalCode();
        } else {
            fetchLaboratoryCode();
        }
    }
});
</script>
@endpush
