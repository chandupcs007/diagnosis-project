<div class="sqa-tab-section">
    <div class="sqa-tab-form-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-plus-circle me-2"></i>Add Radiology Result</h6>
        <form method="POST" action="{{ route('visits.radiology-results.store', $visit) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Study Type <span class="text-danger">*</span></label>
                    <input type="text" name="study_type" class="form-control" placeholder="X-Ray, CT Scan, MRI..." value="{{ old('study_type') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Body Part</label>
                    <input type="text" name="body_part" class="form-control" placeholder="Chest, Abdomen..." value="{{ old('body_part') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="completed" @selected(old('status', 'completed') === 'completed')>Completed</option>
                        <option value="pending" @selected(old('status') === 'pending')>Pending</option>
                        <option value="reviewed" @selected(old('status') === 'reviewed')>Reviewed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Reported At</label>
                    <input type="datetime-local" name="reported_at" class="form-control" value="{{ old('reported_at', now()->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Findings</label>
                    <textarea name="findings" class="form-control" rows="3" placeholder="Radiological findings...">{{ old('findings') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Impression</label>
                    <textarea name="impression" class="form-control" rows="3" placeholder="Clinical impression...">{{ old('impression') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Save Radiology Result</button>
                </div>
            </div>
        </form>
    </div>

    <div class="sqa-tab-records-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-list me-2"></i>Radiology Results</h6>
        @if($visit->radiologyResults->count() > 0)
            <div class="sqa-radiology-cards">
                @foreach($visit->radiologyResults as $result)
                    <div class="sqa-radiology-card">
                        <div class="sqa-radiology-card-header">
                            <div>
                                <strong>{{ $result->study_type }}</strong>
                                @if($result->body_part)
                                    <span class="text-muted"> · {{ $result->body_part }}</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $result->status === 'pending' ? 'warning' : ($result->status === 'reviewed' ? 'success' : 'info') }}">
                                    {{ ucfirst($result->status) }}
                                </span>
                                <small class="text-muted">{{ $result->reported_at?->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>
                        @if($result->findings)
                            <div class="sqa-radiology-card-section">
                                <span class="sqa-radiology-label">Findings</span>
                                <p>{{ $result->findings }}</p>
                            </div>
                        @endif
                        @if($result->impression)
                            <div class="sqa-radiology-card-section">
                                <span class="sqa-radiology-label">Impression</span>
                                <p>{{ $result->impression }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            @include('visits.partials.tabs.empty-state', ['message' => 'No radiology results recorded yet.'])
        @endif
    </div>
</div>
