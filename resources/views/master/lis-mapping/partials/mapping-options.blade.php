<div class="row g-3">
    <div class="col-md-4">
        <a href="{{ route('master.lis-mapping.analyzers') }}"
           class="card h-100 text-decoration-none lis-mapping-option {{ request()->routeIs('master.lis-mapping.analyzers*') ? 'border-primary' : '' }}">
            <div class="card-body">
                <h6 class="font-weight-bold text-gray-800 mb-2">
                    <i class="fas fa-microscope me-1 text-primary"></i> Analyzer Mapping
                </h6>
                <p class="text-muted small mb-0">Register analyzers (name and analyzer ID) in <strong>analyzer_details</strong>.</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('master.lis-mapping.analyzer-assay-codes') }}"
           class="card h-100 text-decoration-none lis-mapping-option {{ request()->routeIs('master.lis-mapping.analyzer-assay-codes') ? 'border-primary' : '' }}">
            <div class="card-body">
                <h6 class="font-weight-bold text-gray-800 mb-2">
                    <i class="fas fa-vial me-1 text-success"></i> Analyzer Assay Code Mapping
                </h6>
                <p class="text-muted small mb-0">Map analyzer assay codes to laboratory catalog tests.</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('master.lis-mapping.assay-his-codes') }}"
           class="card h-100 text-decoration-none lis-mapping-option {{ request()->routeIs('master.lis-mapping.assay-his-codes') ? 'border-primary' : '' }}">
            <div class="card-body">
                <h6 class="font-weight-bold text-gray-800 mb-2">
                    <i class="fas fa-exchange-alt me-1 text-info"></i> Assay Code to HIS Code Mapping
                </h6>
                <p class="text-muted small mb-0">Link assay codes to hospital information system (HIS) codes.</p>
            </div>
        </a>
    </div>
</div>

@once
    @push('styles')
    <style>
        .lis-mapping-option {
            transition: box-shadow 0.15s ease, border-color 0.15s ease;
        }
        .lis-mapping-option:hover {
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.08);
        }
    </style>
    @endpush
@endonce
