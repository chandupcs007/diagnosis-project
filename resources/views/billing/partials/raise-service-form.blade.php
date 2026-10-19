@php
    $formId = $formId ?? 'billing-form';
    $submitLabel = $submitLabel ?? 'Raise Service';
    $showRedirectFields = $showRedirectFields ?? false;
@endphp

<form method="POST" action="{{ route('visits.billing-services.store', $visit) }}" id="{{ $formId }}">
    @csrf
    @if($showRedirectFields)
        <input type="hidden" name="redirect_to" value="billing">
        <input type="hidden" name="from" value="{{ $section ?? '' }}">
    @endif

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Service Type <span class="text-danger">*</span></label>
            <select name="service_type" id="{{ $formId }}-service_type" class="form-control raise-service-type" required>
                <option value="all" {{ old('service_type', 'all') === 'all' ? 'selected' : '' }}>All (Hospital &amp; Laboratory)</option>
                <option value="hospital" {{ old('service_type') === 'hospital' ? 'selected' : '' }}>Hospital Service</option>
                <option value="laboratory" {{ old('service_type') === 'laboratory' ? 'selected' : '' }}>Laboratory</option>
            </select>
        </div>
    </div>

    <div id="{{ $formId }}-all-panel" class="row g-3 raise-all-panel">
        <div class="col-md-12">
            <label class="form-label">Service from Billing Master <span class="text-danger">*</span></label>
            <select name="billing_item_code" id="{{ $formId }}-billing_item_code" class="form-control">
                <option value="">Select service...</option>
                @foreach($billingCatalog as $item)
                    @php
                        $label = trim(($item->item_description ?: $item->item_long_description) . ' (' . $item->item_code . ')');
                        $typeHint = strtoupper((string) $item->item_type) === 'LT' || str_starts_with($item->item_code, '_') ? 'Lab' : 'Hospital';
                    @endphp
                    <option value="{{ $item->item_code }}"
                        data-name="{{ $item->item_description ?: $item->item_long_description }}"
                        data-code="{{ $item->item_code }}"
                        data-price="{{ $item->item_unit_cost }}"
                        data-kind="{{ $typeHint }}"
                        {{ old('billing_item_code') === $item->item_code ? 'selected' : '' }}>
                        [{{ $typeHint }}] {{ $label }} — {{ number_format((float) $item->item_unit_cost, 2) }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Pick an existing hospital or laboratory item from billing_master.</small>
        </div>
    </div>

    <div id="{{ $formId }}-hospital-panel" class="row g-3 raise-hospital-panel" style="display: none;">
        <div class="col-md-6">
            <label class="form-label">Hospital Service Category <span class="text-danger">*</span></label>
            <select id="{{ $formId }}-category_nr" name="category_nr" class="form-control">
                <option value="">Select category...</option>
                @foreach($hospitalCategories as $category)
                    <option value="{{ $category->nr }}"
                        {{ (string) old('category_nr') === (string) $category->nr ? 'selected' : '' }}>
                        {{ $category->category }} — {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Item Code</label>
            <input type="text" class="form-control bg-light" id="{{ $formId }}-hospital_item_code" readonly
                   placeholder="Select category to generate code">
        </div>
        <div class="col-md-8">
            <label class="form-label">Service Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control raise-hospital-name" maxlength="250"
                   placeholder="Consultation, X-Ray..."
                   value="{{ old('service_type') === 'hospital' ? old('service_name') : '' }}">
        </div>
    </div>

    <div id="{{ $formId }}-laboratory-panel" class="row g-3 raise-laboratory-panel" style="display: none;">
        <div class="col-md-6">
            <label class="form-label">Laboratory Department <span class="text-danger">*</span></label>
            <select id="{{ $formId }}-department" name="department" class="form-control">
                <option value="">Select department...</option>
                @foreach($labDepartments as $key => $department)
                    <option value="{{ $key }}" {{ old('department') === $key ? 'selected' : '' }}>
                        {{ $department['name'] }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Item Code</label>
            <input type="text" class="form-control bg-light" id="{{ $formId }}-lab_item_code" readonly
                   placeholder="Select department to generate code">
        </div>
        <div class="col-md-12">
            <label class="form-label">Test / Service Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control raise-laboratory-name" maxlength="250"
                   placeholder="Enter test name"
                   value="{{ old('service_type') === 'laboratory' ? old('service_name') : '' }}">
        </div>
        <div class="col-12">
            <div class="alert alert-info py-2 mb-0">
                <small><i class="fas fa-info-circle me-1"></i> New laboratory items are saved to <strong>lab_test</strong> and <strong>billing_master</strong>, then sent to Sample Acknowledgement.</small>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-3">
            <label class="form-label">Quantity <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" min="1" value="{{ old('quantity', 1) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Unit Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="unit_price" id="{{ $formId }}-unit_price"
                   class="form-control" value="{{ old('unit_price') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Notes</label>
            <input type="text" name="notes" class="form-control" placeholder="Billing notes..." value="{{ old('notes') }}">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> {{ $submitLabel }}
            </button>
        </div>
    </div>
</form>

@once
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const hospitalCodeUrl = @json(route('master.services.next-hospital-code'));
        const laboratoryCodeUrl = @json(route('master.services.next-laboratory-code'));

        document.querySelectorAll('form[id$="billing-form"], form#billing-form').forEach(function (form) {
            initRaiseServiceForm(form);
        });

        function initRaiseServiceForm(form) {
            const prefix = form.id || 'billing-form';
            const serviceType = form.querySelector('.raise-service-type');
            if (!serviceType) {
                return;
            }

            const allPanel = form.querySelector('.raise-all-panel');
            const hospitalPanel = form.querySelector('.raise-hospital-panel');
            const laboratoryPanel = form.querySelector('.raise-laboratory-panel');
            const billingSelect = form.querySelector('[id$="-billing_item_code"]');
            const categorySelect = form.querySelector('[id$="-category_nr"]');
            const departmentSelect = form.querySelector('[id$="-department"]');
            const hospitalItemCode = form.querySelector('[id$="-hospital_item_code"]');
            const labItemCode = form.querySelector('[id$="-lab_item_code"]');
            const hospitalName = form.querySelector('.raise-hospital-name');
            const laboratoryName = form.querySelector('.raise-laboratory-name');
            const unitPrice = form.querySelector('[id$="-unit_price"]');

            function setMode(mode) {
                allPanel.style.display = mode === 'all' ? 'flex' : 'none';
                hospitalPanel.style.display = mode === 'hospital' ? 'flex' : 'none';
                laboratoryPanel.style.display = mode === 'laboratory' ? 'flex' : 'none';

                syncFieldNames(mode);
            }

            function syncFieldNames(mode) {
                billingSelect?.toggleAttribute('name', mode === 'all');
                categorySelect?.toggleAttribute('name', mode === 'hospital');
                departmentSelect?.toggleAttribute('name', mode === 'laboratory');

                hospitalName?.removeAttribute('name');
                laboratoryName?.removeAttribute('name');

                if (mode === 'hospital') {
                    hospitalName?.setAttribute('name', 'service_name');
                } else if (mode === 'laboratory') {
                    laboratoryName?.setAttribute('name', 'service_name');
                }
            }

            async function fetchHospitalCode() {
                if (!categorySelect || !hospitalItemCode) {
                    return;
                }
                hospitalItemCode.value = '';
                if (!categorySelect.value) {
                    return;
                }
                const url = new URL(hospitalCodeUrl, window.location.origin);
                url.searchParams.set('category_nr', categorySelect.value);
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (response.ok) {
                    const data = await response.json();
                    hospitalItemCode.value = data.item_code || '';
                }
            }

            async function fetchLaboratoryCode() {
                if (!departmentSelect || !labItemCode) {
                    return;
                }
                labItemCode.value = '';
                if (!departmentSelect.value) {
                    return;
                }
                const url = new URL(laboratoryCodeUrl, window.location.origin);
                url.searchParams.set('department', departmentSelect.value);
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (response.ok) {
                    const data = await response.json();
                    labItemCode.value = data.item_code || '';
                }
            }

            function applyCatalogItem() {
                if (!billingSelect || !unitPrice) {
                    return;
                }
                const option = billingSelect.options[billingSelect.selectedIndex];
                if (!option || !option.value) {
                    return;
                }
                if (option.dataset.price) {
                    unitPrice.value = option.dataset.price;
                }
            }

            serviceType.addEventListener('change', function () {
                setMode(serviceType.value);
                if (serviceType.value === 'hospital') {
                    fetchHospitalCode();
                } else if (serviceType.value === 'laboratory') {
                    fetchLaboratoryCode();
                }
            });

            categorySelect?.addEventListener('change', fetchHospitalCode);
            departmentSelect?.addEventListener('change', fetchLaboratoryCode);
            billingSelect?.addEventListener('change', applyCatalogItem);

            setMode(serviceType.value);
            applyCatalogItem();
            if (serviceType.value === 'hospital') {
                fetchHospitalCode();
            } else if (serviceType.value === 'laboratory') {
                fetchLaboratoryCode();
            }
        }
    });
    </script>
    @endpush
@endonce
