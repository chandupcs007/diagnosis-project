<div class="table-responsive">
    <table class="table sqa-data-table mb-3">
        <thead>
            <tr>
                <th>Service</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->service_name }}</strong>
                        @if($item->service_code)
                            <br><small class="text-muted">{{ $item->service_code }}</small>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">${{ number_format($item->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Mode of Payment</strong></td>
                <td class="text-end"><strong>{{ $bill->method_label }}</strong></td>
            </tr>
            <tr>
                <td colspan="3" class="text-end"><strong>Total Bill Amount</strong></td>
                <td class="text-end"><strong>${{ number_format($bill->total_amount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
