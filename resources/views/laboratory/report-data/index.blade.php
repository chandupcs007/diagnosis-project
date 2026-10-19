@extends('layouts.app')

@section('title', 'Report Data')

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Report Data',
    'breadcrumb' => 'Laboratory / Report Data',
])
@endpush

@push('styles')
<style>
    .lab-report-modal .modal-dialog { max-width: 920px; }
    .lab-report-modal .modal-content { border: none; border-radius: 12px; overflow: hidden; }
    .lab-report-modal .modal-header {
        background: linear-gradient(135deg, #283593, #3f51b5);
        color: #fff;
        border: none;
    }
    .lab-report-modal .btn-close { filter: invert(1); }
</style>
@endpush

@section('content')
@include('laboratory.partials.workflow-nav')

<div class="card shadow-sm sqa-list-card">
    <div class="card-header"><h6 class="m-0 font-weight-bold">Authorized Reports</h6></div>
    <div class="card-body">
        @include('laboratory.partials.filters', ['filterAction' => route('laboratory.report-data.index')])

        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 sqa-table">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Sample #</th>
                            <th>Patient</th>
                            <th>Test</th>
                            <th>Result</th>
                            <th>Authorized</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td><strong>{{ $request->request_number }}</strong></td>
                                <td>{{ $request->sample_number }}</td>
                                <td>{{ $request->visit->patient->full_name }}</td>
                                <td>{{ $request->test_name }}</td>
                                <td>{{ $request->result_value }} {{ $request->unit }}</td>
                                <td>{{ $request->authorized_at?->format('d M Y H:i') ?? '—' }}</td>
                                <td class="text-nowrap">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary lab-view-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#labReportModal"
                                            data-report-id="{{ $request->id }}">
                                        <i class="fas fa-eye me-1"></i> View
                                    </button>
                                    <a href="{{ route('laboratory.report-data.print', $request) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       target="_blank"
                                       rel="noopener">
                                        <i class="fas fa-print me-1"></i> Print
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $requests->links() }}</div>
        @else
            <div class="text-center text-muted py-4">No authorized reports yet.</div>
        @endif
    </div>
</div>

<div class="modal fade lab-report-modal" id="labReportModal" tabindex="-1" aria-labelledby="labReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labReportModalLabel">
                    <i class="fas fa-file-medical-alt me-2"></i> Laboratory Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="labReportModalBody">
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                    <p class="mb-0">Loading report...</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <a href="#" id="labReportPrintBtn" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                    <i class="fas fa-print me-1"></i> Print Report
                </a>
            </div>
        </div>
    </div>
</div>

@foreach($requests as $request)
    <template id="lab-report-template-{{ $request->id }}">
        @include('laboratory.partials.report-content', ['labRequest' => $request])
    </template>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('labReportModal');
    const modalBody = document.getElementById('labReportModalBody');
    const printBtn = document.getElementById('labReportPrintBtn');
    const printBaseUrl = @json(url('/laboratory/report-data'));

    document.querySelectorAll('.lab-view-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const reportId = button.dataset.reportId;
            const template = document.getElementById('lab-report-template-' + reportId);

            if (template) {
                modalBody.innerHTML = template.innerHTML;
            } else {
                modalBody.innerHTML = '<div class="p-4 text-danger">Unable to load report.</div>';
            }

            printBtn.href = printBaseUrl + '/' + reportId + '/print';
        });
    });

    modal.addEventListener('hidden.bs.modal', function () {
        modalBody.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                <p class="mb-0">Loading report...</p>
            </div>
        `;
        printBtn.href = '#';
    });
});
</script>
@endpush
