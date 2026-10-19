<div class="sqa-tab-section">
    <div class="sqa-tab-form-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-plus-circle me-2"></i>Record Vitals</h6>
        @if($errors->any() && ($activeTab ?? request('tab')) === 'vitals')
            <div class="alert alert-danger py-2 mb-3">
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('visits.vitals.store', $visit) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Blood Pressure</label>
                    <input type="text" name="blood_pressure" class="form-control" placeholder="120/80" value="{{ old('blood_pressure') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pulse (bpm)</label>
                    <input type="number" name="pulse" class="form-control" placeholder="72" value="{{ old('pulse') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Temperature (°F)</label>
                    <input type="number" step="0.1" name="temperature" class="form-control" placeholder="98.6" value="{{ old('temperature') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Resp. Rate</label>
                    <input type="number" name="respiratory_rate" class="form-control" placeholder="16" value="{{ old('respiratory_rate') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">SpO₂ (%)</label>
                    <input type="number" step="0.1" name="oxygen_saturation" class="form-control" placeholder="98" value="{{ old('oxygen_saturation') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control" placeholder="70" value="{{ old('weight') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Height (cm)</label>
                    <input type="number" step="0.1" name="height" class="form-control" placeholder="170" value="{{ old('height') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recorded At</label>
                    <input type="datetime-local" name="recorded_at" class="form-control" value="{{ old('recorded_at', now()->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Save Vitals</button>
                </div>
            </div>
        </form>

        <div class="sqa-tab-inline-history">
            <h6 class="sqa-tab-section-title"><i class="fas fa-list me-2"></i>Vitals History</h6>
            @if($visit->vitals->count() > 0)
                <div class="table-responsive">
                    <table class="table sqa-data-table mb-0">
                        <thead>
                            <tr>
                                <th>Recorded</th>
                                <th>BP</th>
                                <th>Pulse</th>
                                <th>Temp</th>
                                <th>RR</th>
                                <th>SpO₂</th>
                                <th>Weight</th>
                                <th>Height</th>
                                <th>Notes</th>
                                <th>Saved By</th>
                                <th>Saved At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visit->vitals as $vital)
                                <tr>
                                    <td>{{ $vital->recorded_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $vital->blood_pressure ?? '—' }}</td>
                                    <td>{{ $vital->pulse ?? '—' }}</td>
                                    <td>{{ $vital->temperature ? rtrim(rtrim(number_format((float) $vital->temperature, 1, '.', ''), '0'), '.') . '°F' : '—' }}</td>
                                    <td>{{ $vital->respiratory_rate ?? '—' }}</td>
                                    <td>{{ $vital->oxygen_saturation !== null ? rtrim(rtrim(number_format((float) $vital->oxygen_saturation, 1, '.', ''), '0'), '.') . '%' : '—' }}</td>
                                    <td>{{ $vital->weight !== null ? rtrim(rtrim(number_format((float) $vital->weight, 1, '.', ''), '0'), '.') . ' kg' : '—' }}</td>
                                    <td>{{ $vital->height !== null ? rtrim(rtrim(number_format((float) $vital->height, 1, '.', ''), '0'), '.') . ' cm' : '—' }}</td>
                                    <td>{{ $vital->notes ?? '—' }}</td>
                                    <td>{{ $vital->savedBy?->name ?? '—' }}</td>
                                    <td>{{ $vital->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                @include('visits.partials.tabs.empty-state', ['message' => 'No vitals recorded yet. Use the form above to add the first entry.'])
            @endif
        </div>
    </div>
</div>
