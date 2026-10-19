@php
    $sections = config('diagnosis.pharmacy_sections', []);
    $currentRoute = request()->route()->getName();
@endphp

<div class="card shadow-sm mb-4 sqa-list-card">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap gap-2">
            @foreach($sections as $section)
                @php
                    $routeName = $section['route'];
                    $isActive = $currentRoute === $routeName;
                @endphp
                <a href="{{ route($routeName) }}"
                   class="btn btn-sm {{ $isActive ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="{{ $section['icon'] }} me-1"></i> {{ $section['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>
