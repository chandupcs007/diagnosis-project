<div class="sqa-tab-section">
    <div class="sqa-tab-form-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-plus-circle me-2"></i>Add Lab Result</h6>
        <form method="POST" action="{{ route('visits.lab-results.store', $visit) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Test Name <span class="text-danger">*</span></label>
                    <input type="text" name="test_name" class="form-control" placeholder="CBC, Blood Sugar..." value="{{ old('test_name') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Test Code</label>
                    <input type="text" name="test_code" class="form-control" placeholder="LAB-001" value="{{ old('test_code') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Result <span class="text-danger">*</span></label>
                    <input type="text" name="result_value" class="form-control" placeholder="Value" value="{{ old('result_value') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit</label>
                    <input type="text" name="unit" class="form-control" placeholder="mg/dL" value="{{ old('unit') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="normal" @selected(old('status', 'normal') === 'normal')>Normal</option>
                        <option value="abnormal" @selected(old('status') === 'abnormal')>Abnormal</option>
                        <option value="critical" @selected(old('status') === 'critical')>Critical</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reference Range</label>
                    <input type="text" name="reference_range" class="form-control" placeholder="70-100 mg/dL" value="{{ old('reference_range') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tested At</label>
                    <input type="datetime-local" name="tested_at" class="form-control" value="{{ old('tested_at', now()->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" class="form-control" placeholder="Additional notes..." value="{{ old('notes') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Save Lab Result</button>
                </div>
            </div>
        </form>
    </div>

    <div class="sqa-tab-records-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-list me-2"></i>Lab Results</h6>
        @if($visit->labResults->count() > 0)
            <div class="table-responsive">
                <table class="table sqa-data-table mb-0">
                    <thead>
                        <tr>
                            <th>Tested</th>
                            <th>Test</th>
                            <th>Code</th>
                            <th>Result</th>
                            <th>Reference</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visit->labResults as $result)
                            <tr>
                                <td>{{ $result->tested_at?->format('M d, Y h:i A') ?? '—' }}</td>
                                <td><strong>{{ $result->test_name }}</strong></td>
                                <td>{{ $result->test_code ?? '—' }}</td>
                                <td>{{ $result->result_value }}{{ $result->unit ? ' ' . $result->unit : '' }}</td>
                                <td>{{ $result->reference_range ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $result->status === 'critical' ? 'danger' : ($result->status === 'abnormal' ? 'warning' : 'success') }}">
                                        {{ ucfirst($result->status) }}
                                    </span>
                                </td>
                                <td>{{ $result->notes ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('visits.partials.tabs.empty-state', ['message' => 'No lab results recorded yet.'])
        @endif
    </div>
</div>
