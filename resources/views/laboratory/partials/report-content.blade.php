@php
    $patient = $labRequest->visit->patient;
    $statusClass = match ($labRequest->result_status) {
        'normal' => 'success',
        'critical' => 'danger',
        default => 'warning',
    };
@endphp

<div class="lab-report-sheet">
    <div class="lab-report-header">
        <div class="lab-report-logo">
            <i class="fas fa-flask"></i>
        </div>
        <div class="lab-report-brand">
            <h4 class="lab-report-hospital">{{ $hospitalSetting->hospital_name }}</h4>
            <p class="lab-report-dept">{{ $hospitalSetting->department_name }}</p>
            <p class="lab-report-subtitle">Laboratory Investigation Report</p>
        </div>
        <div class="lab-report-meta">
            <span class="lab-report-badge lab-report-badge--authorized">Authorized</span>
            <div class="lab-report-meta-item"><span>Report Date</span><strong>{{ $labRequest->authorized_at?->format('d M Y') ?? '—' }}</strong></div>
            <div class="lab-report-meta-item"><span>Report Time</span><strong>{{ $labRequest->authorized_at?->format('h:i A') ?? '—' }}</strong></div>
        </div>
    </div>

    <div class="lab-report-patient-grid">
        <div class="lab-report-panel">
            <h6 class="lab-report-panel-title">Patient Information</h6>
            <div class="lab-report-info-row"><span>Name</span><strong>{{ $patient->full_name }}</strong></div>
            <div class="lab-report-info-row"><span>Patient ID</span><strong>{{ $patient->display_id }}</strong></div>
            <div class="lab-report-info-row"><span>Age / Gender</span><strong>{{ $patient->age }} yrs / {{ ucfirst($patient->gender) }}</strong></div>
            <div class="lab-report-info-row"><span>Phone</span><strong>{{ $patient->phone ?? '—' }}</strong></div>
        </div>
        <div class="lab-report-panel">
            <h6 class="lab-report-panel-title">Request Information</h6>
            <div class="lab-report-info-row"><span>Request #</span><strong>{{ $labRequest->request_number }}</strong></div>
            <div class="lab-report-info-row"><span>Sample #</span><strong>{{ $labRequest->sample_number }}</strong></div>
            <div class="lab-report-info-row"><span>Visit #</span><strong>{{ $labRequest->visit->visit_number }}</strong></div>
            <div class="lab-report-info-row"><span>Referring Doctor</span><strong>{{ $labRequest->visit->doctor->name }}</strong></div>
        </div>
        <div class="lab-report-panel">
            <h6 class="lab-report-panel-title">Sample Details</h6>
            <div class="lab-report-info-row"><span>Department</span><strong>{{ $labRequest->department_label }}</strong></div>
            <div class="lab-report-info-row"><span>Sample Type</span><strong>{{ $labRequest->sample_type }}</strong></div>
            <div class="lab-report-info-row"><span>Received</span><strong>{{ $labRequest->received_at?->format('d M Y H:i') ?? '—' }}</strong></div>
            <div class="lab-report-info-row"><span>Reported</span><strong>{{ $labRequest->reported_at?->format('d M Y H:i') ?? '—' }}</strong></div>
        </div>
    </div>

    <div class="lab-report-results">
        <h6 class="lab-report-panel-title">Test Results</h6>
        <div class="table-responsive">
            <table class="table lab-report-table mb-0">
                <thead>
                    <tr>
                        <th>Test Name</th>
                        <th>Result</th>
                        <th>Unit</th>
                        <th>Reference Range</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ $labRequest->test_name }}</strong>
                            @if($labRequest->test_code)
                                <br><small class="text-muted">{{ $labRequest->test_code }}</small>
                            @endif
                        </td>
                        <td class="lab-report-result-value">{{ $labRequest->result_value }}</td>
                        <td>{{ $labRequest->unit ?? '—' }}</td>
                        <td>{{ $labRequest->reference_range ?? '—' }}</td>
                        <td><span class="badge bg-{{ $statusClass }}">{{ ucfirst($labRequest->result_status) }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($labRequest->report_notes)
            <div class="lab-report-notes">
                <strong>Notes:</strong> {{ $labRequest->report_notes }}
            </div>
        @endif
    </div>

    <div class="lab-report-footer">
        <div class="lab-report-signatory">
            <div class="lab-report-sign-line">{{ $labRequest->reportedByUser?->name ?? '—' }}</div>
            <div class="lab-report-sign-label">Reported By</div>
        </div>
        <div class="lab-report-signatory">
            <div class="lab-report-sign-line">{{ $labRequest->authorizedByUser?->name ?? '—' }}</div>
            <div class="lab-report-sign-label">Authorized By</div>
        </div>
        <div class="lab-report-disclaimer">
            This is a computer-generated laboratory report from {{ config('diagnosis.name') }}.
            @if($labRequest->authorization_notes)
                <br><em>{{ $labRequest->authorization_notes }}</em>
            @endif
        </div>
    </div>
</div>
