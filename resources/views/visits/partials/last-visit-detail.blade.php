@php
    $statusColors = [
        'scheduled' => 'warning',
        'in_progress' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];
@endphp

<div class="sqa-last-visit-detail">
    <div class="sqa-last-visit-detail-meta mb-3">
        <div class="row g-2 small">
            <div class="col-md-3"><strong>Date:</strong> {{ $visit->visit_date->format('M d, Y') }}</div>
            <div class="col-md-3"><strong>Time:</strong> {{ date('h:i A', strtotime($visit->visit_time)) }}</div>
            <div class="col-md-3"><strong>Department:</strong> {{ $visit->department }}</div>
            <div class="col-md-3">
                <strong>Status:</strong>
                <span class="badge bg-{{ $statusColors[$visit->status] ?? 'secondary' }}">
                    {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                </span>
            </div>
            <div class="col-md-6"><strong>Doctor:</strong> {{ $visit->doctor->name ?? '—' }}</div>
            @if($visit->symptoms)
                <div class="col-12"><strong>Symptoms:</strong> {{ $visit->symptoms }}</div>
            @endif
        </div>
        <div class="mt-2">
            <a href="{{ route('visits.show', $visit->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-external-link-alt me-1"></i> Open full visit
            </a>
        </div>
    </div>

    <div class="sqa-last-visit-sections">
        <div class="sqa-last-visit-block">
            <h6 class="sqa-last-visit-block-title"><i class="fas fa-heartbeat me-1"></i> Vitals</h6>
            @if($visit->vitals->count())
                <div class="table-responsive">
                    <table class="table table-sm sqa-data-table mb-0">
                        <thead>
                            <tr>
                                <th>Recorded</th>
                                <th>BP</th>
                                <th>Pulse</th>
                                <th>Temp</th>
                                <th>RR</th>
                                <th>SpO₂</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visit->vitals as $vital)
                                <tr>
                                    <td>{{ $vital->recorded_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $vital->blood_pressure ?? '—' }}</td>
                                    <td>{{ $vital->pulse ?? '—' }}</td>
                                    <td>{{ $vital->temperature !== null ? $vital->temperature . '°F' : '—' }}</td>
                                    <td>{{ $vital->respiratory_rate ?? '—' }}</td>
                                    <td>{{ $vital->oxygen_saturation !== null ? $vital->oxygen_saturation . '%' : '—' }}</td>
                                    <td>{{ $vital->notes ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small mb-0">No vitals recorded.</p>
            @endif
        </div>

        <div class="sqa-last-visit-block">
            <h6 class="sqa-last-visit-block-title"><i class="fas fa-diagnoses me-1"></i> Diagnosis</h6>
            @if($visit->diagnoses->count())
                <ul class="list-unstyled small mb-0">
                    @foreach($visit->diagnoses as $item)
                        <li class="mb-1">
                            <strong>{{ $item->diagnosis_name }}</strong>
                            @if($item->diagnosis_code)<span class="text-muted">({{ $item->diagnosis_code }})</span>@endif
                            @if($item->notes)<div class="text-muted">{{ $item->notes }}</div>@endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted small mb-0">No diagnoses recorded.</p>
            @endif
        </div>

        <div class="sqa-last-visit-block">
            <h6 class="sqa-last-visit-block-title"><i class="fas fa-prescription me-1"></i> Prescriptions</h6>
            @if($visit->prescriptions->count())
                <ul class="list-unstyled small mb-0">
                    @foreach($visit->prescriptions as $item)
                        <li class="mb-2">
                            <strong>{{ $item->title ?: 'Prescription' }}</strong>
                            @if($item->notes)<div class="text-muted">{{ $item->notes }}</div>@endif
                            @if($item->image_path)
                                <div class="mt-1">
                                    <img src="{{ $item->image_url }}" alt="Prescription image" class="sqa-last-visit-prescription-img">
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted small mb-0">No prescriptions recorded.</p>
            @endif
        </div>

        <div class="sqa-last-visit-block">
            <h6 class="sqa-last-visit-block-title"><i class="fas fa-stethoscope me-1"></i> Chief Complaints</h6>
            @if($visit->chiefComplaints->count())
                <ul class="list-unstyled small mb-0">
                    @foreach($visit->chiefComplaints as $item)
                        <li class="mb-1">{{ $item->complaint }}@if($item->duration) <span class="text-muted">({{ $item->duration }})</span>@endif</li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted small mb-0">No chief complaints recorded.</p>
            @endif
        </div>

        <div class="sqa-last-visit-block">
            <h6 class="sqa-last-visit-block-title"><i class="fas fa-history me-1"></i> History</h6>
            @if($visit->histories->count())
                <ul class="list-unstyled small mb-0">
                    @foreach($visit->histories as $item)
                        <li class="mb-1"><strong>{{ ucfirst($item->history_type) }}:</strong> {{ $item->description }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted small mb-0">No history recorded.</p>
            @endif
        </div>

        <div class="sqa-last-visit-block">
            <h6 class="sqa-last-visit-block-title"><i class="fas fa-file-invoice-dollar me-1"></i> Billing Services</h6>
            @if($visit->billingServices->count())
                <ul class="list-unstyled small mb-0">
                    @foreach($visit->billingServices as $item)
                        <li class="mb-1">{{ $item->service_name }} × {{ $item->quantity }} — {{ number_format((float) $item->total_amount, 2) }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted small mb-0">No billing services recorded.</p>
            @endif
        </div>

        <div class="sqa-last-visit-block">
            <h6 class="sqa-last-visit-block-title"><i class="fas fa-flask me-1"></i> Lab Results</h6>
            @if($visit->labResults->count())
                <ul class="list-unstyled small mb-0">
                    @foreach($visit->labResults as $item)
                        <li class="mb-1">{{ $item->test_name }}: {{ $item->result_value }}@if($item->unit) {{ $item->unit }}@endif</li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted small mb-0">No lab results recorded.</p>
            @endif
        </div>

        <div class="sqa-last-visit-block">
            <h6 class="sqa-last-visit-block-title"><i class="fas fa-x-ray me-1"></i> Radiology</h6>
            @if($visit->radiologyResults->count())
                <ul class="list-unstyled small mb-0">
                    @foreach($visit->radiologyResults as $item)
                        <li class="mb-1">
                            <strong>{{ $item->study_type }}</strong>@if($item->body_part) <span class="text-muted">({{ $item->body_part }})</span>@endif
                            @if($item->findings)<div>{{ $item->findings }}</div>@endif
                            @if($item->impression)<div class="text-muted"><em>{{ $item->impression }}</em></div>@endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted small mb-0">No radiology results recorded.</p>
            @endif
        </div>
    </div>
</div>
