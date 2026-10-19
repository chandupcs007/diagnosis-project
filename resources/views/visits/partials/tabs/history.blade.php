<div class="sqa-tab-section">
    <div class="sqa-tab-form-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-plus-circle me-2"></i>Add History Entry</h6>
        <form method="POST" action="{{ route('visits.histories.store', $visit) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">History Type <span class="text-danger">*</span></label>
                    <select name="history_type" class="form-select" required>
                        <option value="">Select type</option>
                        <option value="medical" @selected(old('history_type') === 'medical')>Medical</option>
                        <option value="surgical" @selected(old('history_type') === 'surgical')>Surgical</option>
                        <option value="family" @selected(old('history_type') === 'family')>Family</option>
                        <option value="social" @selected(old('history_type') === 'social')>Social</option>
                        <option value="allergy" @selected(old('history_type') === 'allergy')>Allergy</option>
                        <option value="medication" @selected(old('history_type') === 'medication')>Medication</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Describe the history..." required>{{ old('description') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Save History</button>
                </div>
            </div>
        </form>
    </div>

    <div class="sqa-tab-records-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-list me-2"></i>Patient History</h6>
        @if($visit->histories->count() > 0 || $patient->medical_history)
            <div class="table-responsive">
                <table class="table sqa-data-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($patient->medical_history && $visit->histories->isEmpty())
                            <tr>
                                <td>{{ $patient->updated_at->format('M d, Y') }}</td>
                                <td><span class="badge bg-secondary">Medical</span></td>
                                <td>{{ $patient->medical_history }} <small class="text-muted">(from patient profile)</small></td>
                            </tr>
                        @endif
                        @foreach($visit->histories as $history)
                            <tr>
                                <td>{{ $history->created_at->format('M d, Y h:i A') }}</td>
                                <td><span class="badge bg-info text-dark">{{ ucfirst($history->history_type) }}</span></td>
                                <td>{{ $history->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('visits.partials.tabs.empty-state', ['message' => 'No history entries recorded yet.'])
        @endif
    </div>
</div>
