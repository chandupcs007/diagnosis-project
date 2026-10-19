<div class="sqa-tab-section">
    <div class="sqa-tab-form-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-plus-circle me-2"></i>Add Diagnosis</h6>
        @if($errors->any() && ($activeTab ?? request('tab')) === 'diagnosis')
            <div class="alert alert-danger py-2 mb-3">
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('visits.diagnoses.store', $visit) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Diagnosis Code</label>
                    <input type="text" name="diagnosis_code" class="form-control" placeholder="ICD-10 code" value="{{ old('diagnosis_code') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Diagnosis Name <span class="text-danger">*</span></label>
                    <input type="text" name="diagnosis_name" class="form-control" placeholder="Enter diagnosis" value="{{ old('diagnosis_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Severity</label>
                    <select name="severity" class="form-select">
                        <option value="">Select severity</option>
                        <option value="mild" @selected(old('severity') === 'mild')>Mild</option>
                        <option value="moderate" @selected(old('severity') === 'moderate')>Moderate</option>
                        <option value="severe" @selected(old('severity') === 'severe')>Severe</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Clinical notes...">{{ old('notes') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Save Diagnosis</button>
                </div>
            </div>
        </form>
    </div>

    <div class="sqa-tab-records-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-list me-2"></i>Diagnosis Records</h6>
        @if($visit->diagnoses->count() > 0 || $visit->diagnosis)
            <div class="table-responsive">
                <table class="table sqa-data-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Code</th>
                            <th>Diagnosis</th>
                            <th>Severity</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($visit->diagnosis && $visit->diagnoses->isEmpty())
                            <tr>
                                <td>{{ $visit->created_at->format('M d, Y') }}</td>
                                <td>—</td>
                                <td>{{ $visit->diagnosis }}</td>
                                <td><span class="badge bg-secondary">Initial</span></td>
                                <td>From visit registration</td>
                            </tr>
                        @endif
                        @foreach($visit->diagnoses as $diagnosis)
                            <tr>
                                <td>{{ $diagnosis->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $diagnosis->diagnosis_code ?? '—' }}</td>
                                <td><strong>{{ $diagnosis->diagnosis_name }}</strong></td>
                                <td>
                                    @if($diagnosis->severity)
                                        <span class="badge bg-{{ $diagnosis->severity === 'severe' ? 'danger' : ($diagnosis->severity === 'moderate' ? 'warning' : 'success') }}">
                                            {{ ucfirst($diagnosis->severity) }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $diagnosis->notes ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('visits.partials.tabs.empty-state', ['message' => 'No diagnosis recorded yet.'])
        @endif
    </div>
</div>
