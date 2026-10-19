<div class="row" id="service-catalog-manager">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Hospital Services — Search &amp; Update</h6>
            </div>
            <div class="card-body">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" id="hospital_catalog_search" placeholder="Search by code, category, or description..." autocomplete="off">
                    <button type="button" class="btn btn-outline-primary" id="hospital_catalog_search_btn">Search</button>
                </div>
                <div id="hospital_catalog_results" class="catalog-results mb-3">
                    <div class="p-2 text-muted small">Enter a search term and click Search.</div>
                </div>
                <form id="hospital_catalog_form" class="d-none border-top pt-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Item code</label>
                            <input type="text" class="form-control form-control-sm bg-light" id="hospital_edit_code" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Category</label>
                            <input type="text" class="form-control form-control-sm" id="hospital_edit_category" maxlength="10">
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="hospital_edit_description" maxlength="250" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Unit price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="hospital_edit_unit_price" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Work type</label>
                            <input type="text" class="form-control form-control-sm" id="hospital_edit_work_type" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Billing short description</label>
                            <input type="text" class="form-control form-control-sm" id="hospital_edit_billing_description" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Billing long description</label>
                            <input type="text" class="form-control form-control-sm" id="hospital_edit_billing_long_description" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Sub level</label>
                            <input type="number" class="form-control form-control-sm" id="hospital_edit_sub_level">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Navision</label>
                            <input type="number" class="form-control form-control-sm" id="hospital_edit_navision">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Std code</label>
                            <input type="text" class="form-control form-control-sm" id="hospital_edit_std_code" maxlength="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Notes</label>
                            <textarea class="form-control form-control-sm" id="hospital_edit_notes" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Remarks</label>
                            <textarea class="form-control form-control-sm" id="hospital_edit_remarks" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Inclusive</label>
                            <textarea class="form-control form-control-sm" id="hospital_edit_inclusive" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Exclusive</label>
                            <textarea class="form-control form-control-sm" id="hospital_edit_exclusive" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Update hospital service</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm catalog-log-btn" data-service-type="hospital"><i class="fas fa-history me-1"></i> View change log</button>
                    </div>
                    <div class="text-danger small mt-2 d-none" id="hospital_catalog_error"></div>
                    <div class="text-success small mt-2 d-none" id="hospital_catalog_success"></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header"><h6 class="m-0 font-weight-bold">Laboratory Services — Search &amp; Update</h6></div>
            <div class="card-body">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" id="lab_catalog_search" placeholder="Search by code, name, or department..." autocomplete="off">
                    <button type="button" class="btn btn-outline-success" id="lab_catalog_search_btn">Search</button>
                </div>
                <div id="lab_catalog_results" class="catalog-results mb-3">
                    <div class="p-2 text-muted small">Enter a search term and click Search.</div>
                </div>
                <form id="lab_catalog_form" class="d-none border-top pt-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Item code</label>
                            <input type="text" class="form-control form-control-sm bg-light" id="lab_edit_code" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Department</label>
                            <input type="text" class="form-control form-control-sm bg-light" id="lab_edit_department_label" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Is profile</label>
                            <input type="text" class="form-control form-control-sm bg-light" id="lab_edit_is_profile" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Test name / description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="lab_edit_name" maxlength="250" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Sample type <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" id="lab_edit_sample_type" required>
                                @foreach(config('diagnosis.sample_types', []) as $sampleTypeOption)
                                    <option value="{{ $sampleTypeOption }}">{{ $sampleTypeOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Unit price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="lab_edit_unit_price" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Measure unit</label>
                            <input type="text" class="form-control form-control-sm" id="lab_edit_msr_unit" maxlength="80">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Visible on catalog</label>
                            <select class="form-control form-control-sm" id="lab_edit_is_visible">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Normal range</label>
                            <textarea class="form-control form-control-sm" id="lab_edit_normal_range" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Billing short description</label>
                            <input type="text" class="form-control form-control-sm" id="lab_edit_billing_description" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Billing long description</label>
                            <input type="text" class="form-control form-control-sm" id="lab_edit_billing_long_description" maxlength="100">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save me-1"></i> Update laboratory service</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm catalog-log-btn" data-service-type="laboratory"><i class="fas fa-history me-1"></i> View change log</button>
                    </div>
                    <div class="text-danger small mt-2 d-none" id="lab_catalog_error"></div>
                    <div class="text-success small mt-2 d-none" id="lab_catalog_success"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="catalogAuditModal" tabindex="-1" aria-labelledby="catalogAuditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="catalogAuditModalLabel">Service change log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-2" id="catalog_audit_meta"></p>
                <div id="catalog_audit_list" class="mb-3"></div>
                <div id="catalog_audit_detail" class="d-none">
                    <h6 class="mb-2">Change detail</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr><th>Field</th><th>Before</th><th>After</th></tr>
                            </thead>
                            <tbody id="catalog_audit_detail_body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .catalog-results {
        max-height: 180px;
        overflow-y: auto;
        border: 1px solid #e9ecef;
        border-radius: 0.25rem;
        background: #fafafa;
    }
    .catalog-result-item {
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }
    .catalog-result-item:hover,
    .catalog-result-item.active {
        background: #e7f1ff;
    }
    .catalog-log-entry {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
    }
    .catalog-log-entry:hover {
        background: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const routes = {
        hospitalSearch: @json(route('master.services.hospital.search')),
        hospitalShow: @json(url('/master/services/hospital')),
        hospitalLogs: @json(url('/master/services/hospital')),
        labSearch: @json(route('master.services.laboratory.search')),
        labShow: @json(url('/master/services/laboratory')),
        labLogs: @json(url('/master/services/laboratory')),
    };

    const fieldLabels = {
        item_code: 'Item code',
        category: 'Category',
        description: 'Description',
        unit_price: 'Unit price',
        name: 'Test name',
        sample_type: 'Sample type',
        msr_unit: 'Measure unit',
        normal_range: 'Normal range',
        is_visible: 'Visible',
        is_profile: 'Is profile',
        department: 'Department',
        department_label: 'Department',
        billing_description: 'Billing short description',
        billing_long_description: 'Billing long description',
        notes: 'Notes',
        remarks: 'Remarks',
        inclusive: 'Inclusive',
        exclusive: 'Exclusive',
        std_code: 'Std code',
        sub_level: 'Sub level',
        navision: 'Navision',
        work_type: 'Work type',
    };

    let activeHospitalCode = null;
    let activeLabCode = null;
    let auditModal = null;

    function encodeItemCode(code) {
        return encodeURIComponent(code);
    }

    async function jsonFetch(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                ...(options.headers || {}),
            },
            ...options,
        });

        const data = await response.json().catch(function () {
            return {};
        });

        if (!response.ok) {
            throw data;
        }

        return data;
    }

    function renderResults(container, items, type) {
        container.innerHTML = '';
        if (!items.length) {
            container.innerHTML = '<div class="p-2 text-muted small">No matching services.</div>';
            return;
        }

        items.forEach(function (item) {
            const row = document.createElement('div');
            row.className = 'catalog-result-item';
            if (type === 'hospital') {
                row.innerHTML = '<strong>' + escapeHtml(item.code) + '</strong> · ' + escapeHtml(item.category) + ' — ' + escapeHtml(item.description);
                row.addEventListener('click', function () {
                    loadHospitalService(item.code);
                });
            } else {
                row.innerHTML = '<strong>' + escapeHtml(item.id) + '</strong> — ' + escapeHtml(item.name);
                row.addEventListener('click', function () {
                    loadLabService(item.id);
                });
            }
            container.appendChild(row);
        });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showMessage(el, message, isError) {
        el.textContent = message;
        el.classList.toggle('d-none', !message);
        el.classList.toggle('text-danger', isError);
        el.classList.toggle('text-success', !isError);
    }

    function showCatalogIdleMessage(container) {
        container.innerHTML = '<div class="p-2 text-muted small">Enter a search term and click Search.</div>';
    }

    async function searchHospital() {
        const container = document.getElementById('hospital_catalog_results');
        const q = document.getElementById('hospital_catalog_search').value.trim();
        if (!q) {
            showCatalogIdleMessage(container);
            return;
        }
        const url = new URL(routes.hospitalSearch, window.location.origin);
        url.searchParams.set('q', q);
        const data = await jsonFetch(url);
        renderResults(container, data.results || [], 'hospital');
    }

    async function searchLab() {
        const container = document.getElementById('lab_catalog_results');
        const q = document.getElementById('lab_catalog_search').value.trim();
        if (!q) {
            showCatalogIdleMessage(container);
            return;
        }
        const url = new URL(routes.labSearch, window.location.origin);
        url.searchParams.set('q', q);
        const data = await jsonFetch(url);
        renderResults(container, data.results || [], 'laboratory');
    }

    async function loadHospitalService(code) {
        activeHospitalCode = code;
        const data = await jsonFetch(routes.hospitalShow + '/' + encodeItemCode(code));
        const s = data.service;
        document.getElementById('hospital_catalog_form').classList.remove('d-none');
        document.getElementById('hospital_edit_code').value = s.item_code;
        document.getElementById('hospital_edit_category').value = s.category ?? '';
        document.getElementById('hospital_edit_description').value = s.description ?? '';
        document.getElementById('hospital_edit_unit_price').value = s.unit_price ?? 0;
        document.getElementById('hospital_edit_work_type').value = s.work_type ?? '';
        document.getElementById('hospital_edit_billing_description').value = s.billing_description ?? '';
        document.getElementById('hospital_edit_billing_long_description').value = s.billing_long_description ?? '';
        document.getElementById('hospital_edit_sub_level').value = s.sub_level ?? 0;
        document.getElementById('hospital_edit_navision').value = s.navision ?? 0;
        document.getElementById('hospital_edit_std_code').value = s.std_code ?? '';
        document.getElementById('hospital_edit_notes').value = s.notes ?? '';
        document.getElementById('hospital_edit_remarks').value = s.remarks ?? '';
        document.getElementById('hospital_edit_inclusive').value = s.inclusive ?? '';
        document.getElementById('hospital_edit_exclusive').value = s.exclusive ?? '';
        showMessage(document.getElementById('hospital_catalog_error'), '', true);
        showMessage(document.getElementById('hospital_catalog_success'), '', false);
    }

    async function loadLabService(code) {
        activeLabCode = code;
        const data = await jsonFetch(routes.labShow + '/' + encodeItemCode(code));
        const s = data.service;
        document.getElementById('lab_catalog_form').classList.remove('d-none');
        document.getElementById('lab_edit_code').value = s.item_code;
        document.getElementById('lab_edit_department_label').value = s.department_label ?? s.department;
        document.getElementById('lab_edit_is_profile').value = s.is_profile ? 'Yes (profile)' : 'No';
        document.getElementById('lab_edit_name').value = s.name ?? '';
        document.getElementById('lab_edit_sample_type').value = s.sample_type || 'Serum';
        document.getElementById('lab_edit_unit_price').value = s.unit_price ?? 0;
        document.getElementById('lab_edit_msr_unit').value = s.msr_unit ?? '';
        document.getElementById('lab_edit_is_visible').value = s.is_visible ? '1' : '0';
        document.getElementById('lab_edit_normal_range').value = s.normal_range ?? '';
        document.getElementById('lab_edit_billing_description').value = s.billing_description ?? '';
        document.getElementById('lab_edit_billing_long_description').value = s.billing_long_description ?? '';
        showMessage(document.getElementById('lab_catalog_error'), '', true);
        showMessage(document.getElementById('lab_catalog_success'), '', false);
    }

    document.getElementById('hospital_catalog_search_btn').addEventListener('click', function () {
        searchHospital().catch(function (err) {
            alert(err.message || 'Hospital search failed.');
        });
    });
    document.getElementById('lab_catalog_search_btn').addEventListener('click', function () {
        searchLab().catch(function (err) {
            alert(err.message || 'Laboratory search failed.');
        });
    });
    document.getElementById('hospital_catalog_search').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            document.getElementById('hospital_catalog_search_btn').click();
        }
    });
    document.getElementById('lab_catalog_search').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            document.getElementById('lab_catalog_search_btn').click();
        }
    });

    document.getElementById('hospital_catalog_form').addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!activeHospitalCode) {
            return;
        }
        const payload = {
            category: document.getElementById('hospital_edit_category').value,
            description: document.getElementById('hospital_edit_description').value,
            unit_price: document.getElementById('hospital_edit_unit_price').value,
            work_type: document.getElementById('hospital_edit_work_type').value,
            billing_description: document.getElementById('hospital_edit_billing_description').value,
            billing_long_description: document.getElementById('hospital_edit_billing_long_description').value,
            sub_level: document.getElementById('hospital_edit_sub_level').value,
            navision: document.getElementById('hospital_edit_navision').value,
            std_code: document.getElementById('hospital_edit_std_code').value,
            notes: document.getElementById('hospital_edit_notes').value,
            remarks: document.getElementById('hospital_edit_remarks').value,
            inclusive: document.getElementById('hospital_edit_inclusive').value,
            exclusive: document.getElementById('hospital_edit_exclusive').value,
        };

        try {
            const data = await jsonFetch(routes.hospitalShow + '/' + encodeItemCode(activeHospitalCode), {
                method: 'PUT',
                body: JSON.stringify(payload),
            });
            showMessage(document.getElementById('hospital_catalog_success'), data.message || 'Updated.', false);
            showMessage(document.getElementById('hospital_catalog_error'), '', true);
            if (document.getElementById('hospital_catalog_search').value.trim()) {
                searchHospital();
            }
        } catch (err) {
            const msg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Update failed.');
            showMessage(document.getElementById('hospital_catalog_error'), msg, true);
        }
    });

    document.getElementById('lab_catalog_form').addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!activeLabCode) {
            return;
        }
        const payload = {
            name: document.getElementById('lab_edit_name').value,
            sample_type: document.getElementById('lab_edit_sample_type').value,
            unit_price: document.getElementById('lab_edit_unit_price').value,
            msr_unit: document.getElementById('lab_edit_msr_unit').value,
            is_visible: document.getElementById('lab_edit_is_visible').value,
            normal_range: document.getElementById('lab_edit_normal_range').value,
            billing_description: document.getElementById('lab_edit_billing_description').value,
            billing_long_description: document.getElementById('lab_edit_billing_long_description').value,
        };

        try {
            const data = await jsonFetch(routes.labShow + '/' + encodeItemCode(activeLabCode), {
                method: 'PUT',
                body: JSON.stringify(payload),
            });
            showMessage(document.getElementById('lab_catalog_success'), data.message || 'Updated.', false);
            showMessage(document.getElementById('lab_catalog_error'), '', true);
            if (document.getElementById('lab_catalog_search').value.trim()) {
                searchLab();
            }
        } catch (err) {
            const msg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Update failed.');
            showMessage(document.getElementById('lab_catalog_error'), msg, true);
        }
    });

    function formatValue(value) {
        if (value === null || value === undefined) {
            return '—';
        }
        if (typeof value === 'boolean') {
            return value ? 'Yes' : 'No';
        }
        return String(value);
    }

    function renderAuditDetail(before, after) {
        const body = document.getElementById('catalog_audit_detail_body');
        body.innerHTML = '';
        const keys = new Set([...Object.keys(before || {}), ...Object.keys(after || {})]);
        const changed = [];

        keys.forEach(function (key) {
            if (key === 'item_code') {
                return;
            }
            const b = before?.[key];
            const a = after?.[key];
            if (JSON.stringify(b) !== JSON.stringify(a)) {
                changed.push(key);
            }
        });

        if (!changed.length) {
            body.innerHTML = '<tr><td colspan="3" class="text-muted">No field differences recorded.</td></tr>';
        } else {
            changed.sort().forEach(function (key) {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td>' + escapeHtml(fieldLabels[key] || key) + '</td>' +
                    '<td>' + escapeHtml(formatValue(before?.[key])) + '</td>' +
                    '<td>' + escapeHtml(formatValue(after?.[key])) + '</td>';
                body.appendChild(tr);
            });
        }

        document.getElementById('catalog_audit_detail').classList.remove('d-none');
    }

    async function openAuditLog(serviceType) {
        const code = serviceType === 'hospital' ? activeHospitalCode : activeLabCode;
        if (!code) {
            alert('Select a service first.');
            return;
        }

        const base = serviceType === 'hospital' ? routes.hospitalLogs : routes.labLogs;
        const data = await jsonFetch(base + '/' + encodeItemCode(code) + '/logs');
        const list = document.getElementById('catalog_audit_list');
        list.innerHTML = '';
        document.getElementById('catalog_audit_detail').classList.add('d-none');
        document.getElementById('catalog_audit_meta').textContent = 'Item: ' + code + ' · ' + (data.logs?.length || 0) + ' change(s)';

        if (!data.logs?.length) {
            list.innerHTML = '<p class="text-muted small mb-0">No changes logged yet for this item.</p>';
        } else {
            data.logs.forEach(function (log) {
                const entry = document.createElement('div');
                entry.className = 'catalog-log-entry';
                entry.innerHTML = '<strong>' + escapeHtml(log.changed_at) + '</strong><br>' +
                    '<span class="small text-muted">By: ' + escapeHtml(log.changed_by) + '</span>';
                entry.addEventListener('click', function () {
                    renderAuditDetail(log.before_data, log.after_data);
                });
                list.appendChild(entry);
            });
        }

        if (!auditModal) {
            auditModal = new bootstrap.Modal(document.getElementById('catalogAuditModal'));
        }
        auditModal.show();
    }

    document.querySelectorAll('.catalog-log-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openAuditLog(btn.dataset.serviceType).catch(function () {
                alert('Could not load change log.');
            });
        });
    });

});
</script>
@endpush
