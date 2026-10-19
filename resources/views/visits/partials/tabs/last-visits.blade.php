<div class="sqa-tab-section">
    <div class="sqa-tab-form-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-calendar-check me-2"></i>Last Visits</h6>
        <p class="text-muted small mb-3">Select a previous visit number to view all recorded information for that encounter.</p>

        @if($previousVisits->count() > 0)
            <div class="sqa-last-visits-layout">
                <div class="sqa-last-visits-list" role="tablist" aria-label="Previous visits">
                    @foreach($previousVisits as $index => $prevVisit)
                        <button type="button"
                                class="sqa-last-visit-link {{ ($loop->first && !request('previous_visit')) || (string) request('previous_visit') === (string) $prevVisit->id ? 'active' : '' }}"
                                data-last-visit-id="{{ $prevVisit->id }}"
                                role="tab"
                                aria-selected="{{ ($loop->first && !request('previous_visit')) || (string) request('previous_visit') === (string) $prevVisit->id ? 'true' : 'false' }}">
                            <span class="sqa-last-visit-number">{{ $prevVisit->visit_number }}</span>
                            <span class="sqa-last-visit-date">{{ $prevVisit->visit_date->format('M d, Y') }}</span>
                            <span class="sqa-last-visit-doctor">{{ $prevVisit->doctor->name ?? '—' }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="sqa-last-visits-detail">
                    @foreach($previousVisits as $prevVisit)
                        <div class="sqa-last-visit-panel {{ ($loop->first && !request('previous_visit')) || (string) request('previous_visit') === (string) $prevVisit->id ? 'active' : '' }}"
                             id="last-visit-panel-{{ $prevVisit->id }}"
                             role="tabpanel"
                             data-last-visit-panel="{{ $prevVisit->id }}">
                            <div class="sqa-last-visit-panel-header">
                                <h6 class="mb-0">Visit {{ $prevVisit->visit_number }}</h6>
                            </div>
                            @include('visits.partials.last-visit-detail', ['visit' => $prevVisit])
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            @include('visits.partials.tabs.empty-state', ['message' => 'No previous visits found for this patient.'])
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const links = document.querySelectorAll('.sqa-last-visit-link');
        const panels = document.querySelectorAll('[data-last-visit-panel]');

        if (!links.length) {
            return;
        }

        function showPreviousVisit(visitId) {
            links.forEach(function (link) {
                const isActive = link.getAttribute('data-last-visit-id') === String(visitId);
                link.classList.toggle('active', isActive);
                link.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            panels.forEach(function (panel) {
                const isActive = panel.getAttribute('data-last-visit-panel') === String(visitId);
                panel.classList.toggle('active', isActive);
            });

            const url = new URL(window.location);
            url.searchParams.set('tab', 'last-visits');
            url.searchParams.set('previous_visit', visitId);
            history.replaceState(null, '', url);
        }

        links.forEach(function (link) {
            link.addEventListener('click', function () {
                showPreviousVisit(this.getAttribute('data-last-visit-id'));
            });
        });
    });
</script>
@endpush
