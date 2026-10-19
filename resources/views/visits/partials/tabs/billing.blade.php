<div class="sqa-tab-section">
    <div class="sqa-tab-form-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-plus-circle me-2"></i>Add Billing Service</h6>
        @include('billing.partials.raise-service-form', [
            'visit' => $visit,
            'formId' => 'visit-billing-form',
            'submitLabel' => 'Add',
            'showRedirectFields' => false,
        ])
    </div>

    <div class="sqa-tab-records-panel">
        <h6 class="sqa-tab-section-title"><i class="fas fa-list me-2"></i>Billing Services</h6>
        @if($visit->billingServices->count() > 0)
            <div class="table-responsive">
                <table class="table sqa-data-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Service</th>
                            <th>Code</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Lab Request</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($visit->billingServices as $service)
                            @php $grandTotal += $service->total_amount; @endphp
                            <tr>
                                <td>{{ $service->created_at->format('M d, Y') }}</td>
                                <td><strong>{{ $service->service_name }}</strong></td>
                                <td>{{ $service->service_code ?? '—' }}</td>
                                <td>{{ $service->quantity }}</td>
                                <td>${{ number_format($service->unit_price, 2) }}</td>
                                <td><strong>${{ number_format($service->total_amount, 2) }}</strong></td>
                                <td>
                                    @if($service->labRequest)
                                        <a href="{{ route($service->labRequest->workflow_show_route_name, $service->labRequest) }}" class="badge bg-info text-decoration-none">
                                            {{ $service->labRequest->request_number }}
                                        </a>
                                        <br><small class="text-muted">{{ $service->labRequest->status_label }}</small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $service->notes ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="sqa-billing-total">
                            <td colspan="5" class="text-end"><strong>Grand Total</strong></td>
                            <td colspan="3"><strong>${{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            @include('visits.partials.tabs.empty-state', ['message' => 'No billing services added yet.'])
        @endif
    </div>
</div>
