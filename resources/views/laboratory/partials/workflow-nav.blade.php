@php
    $stages = config('diagnosis.lab_workflow_stages');
    $currentRoute = request()->route()->getName();
@endphp

<div class="card shadow-sm mb-4 sqa-list-card">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('laboratory.index') }}"
               class="btn btn-sm {{ $currentRoute === 'laboratory.index' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-flask me-1"></i> Laboratory Home
            </a>
            @foreach($stages as $key => $stage)
                <a href="{{ route($stage['route']) }}"
                   class="btn btn-sm {{ str_starts_with($currentRoute, str_replace('.index', '', $stage['route'])) ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="{{ $stage['icon'] }} me-1"></i> {{ $stage['label'] }}
                    @if(isset($stageCounts) && isset($stageCounts[$key]))
                        <span class="badge bg-light text-dark ms-1">{{ $stageCounts[$key] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
