<div class="sqa-tab-section">
    <div class="sqa-tab-form-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-plus-circle me-2"></i>Add Chief Complaint</h6>
        <form method="POST" action="{{ route('visits.chief-complaints.store', $visit) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Complaint <span class="text-danger">*</span></label>
                    <textarea name="complaint" class="form-control" rows="2" placeholder="Describe the chief complaint..." required>{{ old('complaint') }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Duration</label>
                    <input type="text" name="duration" class="form-control" placeholder="e.g. 3 days" value="{{ old('duration') }}">
                </div>
                <div class="col-md-3">
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
                    <textarea name="notes" class="form-control" rows="2" placeholder="Additional details...">{{ old('notes') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Save Complaint</button>
                </div>
            </div>
        </form>
    </div>

    <div class="sqa-tab-records-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-list me-2"></i>Chief Complaints</h6>
        @if($visit->chiefComplaints->count() > 0 || $visit->symptoms)
            <div class="table-responsive">
                <table class="table sqa-data-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Complaint</th>
                            <th>Duration</th>
                            <th>Severity</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($visit->symptoms && $visit->chiefComplaints->isEmpty())
                            <tr>
                                <td>{{ $visit->created_at->format('M d, Y') }}</td>
                                <td>{{ $visit->symptoms }}</td>
                                <td>—</td>
                                <td><span class="badge bg-secondary">Initial</span></td>
                                <td>From visit registration</td>
                            </tr>
                        @endif
                        @foreach($visit->chiefComplaints as $complaint)
                            <tr>
                                <td>{{ $complaint->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $complaint->complaint }}</td>
                                <td>{{ $complaint->duration ?? '—' }}</td>
                                <td>
                                    @if($complaint->severity)
                                        <span class="badge bg-{{ $complaint->severity === 'severe' ? 'danger' : ($complaint->severity === 'moderate' ? 'warning' : 'success') }}">
                                            {{ ucfirst($complaint->severity) }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $complaint->notes ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('visits.partials.tabs.empty-state', ['message' => 'No chief complaints recorded yet.'])
        @endif
    </div>
</div>
