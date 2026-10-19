@php
    use App\Support\VitalIndicator;

    $latestVital = $visit->vitals->first();
    $vitalItems = $latestVital ? VitalIndicator::summary($latestVital) : [];
@endphp

@if(count($vitalItems) > 0)
    <div class="sqa-visit-vitals-strip">
        <div class="sqa-visit-vitals-label">
            <i class="fas fa-heartbeat me-1"></i>Latest Vitals
            @if($latestVital?->recorded_at)
                <span class="sqa-visit-vitals-time">{{ $latestVital->recorded_at->format('h:i A') }}</span>
            @endif
        </div>
        <div class="sqa-visit-vitals-chips">
            @foreach($vitalItems as $item)
                @php
                    $statusClass = match($item['status']) {
                        'high' => 'sqa-vital-high',
                        'low' => 'sqa-vital-low',
                        'normal' => 'sqa-vital-normal',
                        default => 'sqa-vital-neutral',
                    };
                    $flagIcon = match($item['status']) {
                        'high' => 'fa-arrow-up',
                        'low' => 'fa-arrow-down',
                        default => null,
                    };
                    $flagLabel = match($item['status']) {
                        'high' => 'High',
                        'low' => 'Low',
                        'normal' => 'Normal',
                        default => null,
                    };
                @endphp
                <span class="sqa-vital-chip {{ $statusClass }}" title="{{ $item['label'] }}: {{ $item['value'] }}{{ $flagLabel ? ' (' . $flagLabel . ')' : '' }}">
                    <span class="sqa-vital-chip-label">{{ $item['label'] }}</span>
                    <span class="sqa-vital-chip-value">{{ $item['value'] }}</span>
                    @if($flagIcon)
                        <span class="sqa-vital-flag sqa-vital-flag-{{ $item['status'] }}">
                            <i class="fas {{ $flagIcon }}"></i>
                        </span>
                    @endif
                </span>
            @endforeach
        </div>
    </div>
@endif
