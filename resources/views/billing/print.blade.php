<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $bill->bill_number }} — Invoice</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            background: #eef2f1;
            color: #212121;
            font-family: 'Roboto', sans-serif;
        }
        .invoice-actions {
            max-width: 860px;
            margin: 0 auto 12px;
            text-align: right;
        }
        .invoice-actions button {
            border: 0;
            border-radius: 2px;
            padding: 8px 14px;
            margin-left: 8px;
            font-size: 13px;
            cursor: pointer;
        }
        .invoice-actions .print { background: #3f51b5; color: #fff; }
        .invoice-actions .close { background: #eceff1; color: #212121; }
        .invoice-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #fff;
            padding: 28px 32px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .invoice-header {
            display: flex;
            gap: 16px;
            align-items: center;
            border-bottom: 3px solid #00acc1;
            padding-bottom: 16px;
        }
        .invoice-logo {
            width: 84px;
            height: 84px;
            object-fit: contain;
        }
        .invoice-brand h1 {
            margin: 0 0 4px;
            font-size: 22px;
            color: #1a237e;
        }
        .invoice-brand p { margin: 0; color: #616161; font-size: 13px; line-height: 1.45; }
        .invoice-title {
            margin-left: auto;
            text-align: right;
            font-size: 13px;
            color: #616161;
        }
        .invoice-title strong {
            display: block;
            color: #1a237e;
            font-size: 18px;
            letter-spacing: 0.4px;
        }
        .invoice-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 18px;
        }
        .invoice-panel {
            border: 1px solid #e0e0e0;
            padding: 12px 14px;
        }
        .invoice-panel h2 {
            margin: 0 0 8px;
            font-size: 12px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #00838f;
        }
        .invoice-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            padding: 3px 0;
        }
        .invoice-row span { color: #757575; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            font-size: 13px;
        }
        th, td { padding: 8px 6px; border-bottom: 1px solid #eceff1; text-align: left; }
        th {
            font-size: 11px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #757575;
        }
        td.num, th.num { text-align: right; }
        tfoot td { border-bottom: 0; }
        .invoice-total td { font-size: 15px; color: #1a237e; }
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-actions { display: none !important; }
            .invoice-sheet { box-shadow: none; max-width: none; padding: 0; }
        }
    </style>
</head>
<body>
    @php
        $visit = $bill->visit;
        $patient = $visit->patient;
        $logo = $hospitalSetting->logo_url ?: asset('images/carevision-brand.jpg');
    @endphp

    <div class="invoice-actions">
        <button type="button" class="print" onclick="window.print()">Print</button>
        <button type="button" class="close" onclick="window.close()">Close</button>
    </div>

    <div class="invoice-sheet">
        <div class="invoice-header">
            <img class="invoice-logo" src="{{ $logo }}" alt="{{ $hospitalSetting->hospital_name }}">
            <div class="invoice-brand">
                <h1>{{ $hospitalSetting->hospital_name }}</h1>
                <p>{{ $hospitalSetting->department_name }}</p>
                @if($hospitalSetting->address)
                    <p>{{ $hospitalSetting->address }}</p>
                @endif
                <p>
                    @if($hospitalSetting->contact_phone){{ $hospitalSetting->contact_phone }}@endif
                    @if($hospitalSetting->contact_email) · {{ $hospitalSetting->contact_email }}@endif
                    @if($hospitalSetting->website) · {{ $hospitalSetting->website }}@endif
                </p>
            </div>
            <div class="invoice-title">
                <strong>INVOICE</strong>
                {{ $bill->invoice_number }}
            </div>
        </div>

        <div class="invoice-grid">
            <div class="invoice-panel">
                <h2>Patient</h2>
                <div class="invoice-row"><span>Patient ID</span><strong>{{ $patient->display_id }}</strong></div>
                <div class="invoice-row"><span>Visit Number</span><strong>{{ $visit->visit_number }}</strong></div>
                <div class="invoice-row"><span>Patient Name</span><strong>{{ $patient->full_name }}</strong></div>
                <div class="invoice-row"><span>Doctor</span><strong>{{ $visit->doctor ? 'Dr. ' . $visit->doctor->name : '—' }}</strong></div>
                <div class="invoice-row"><span>Department</span><strong>{{ $visit->department }}</strong></div>
            </div>
            <div class="invoice-panel">
                <h2>Receipt</h2>
                <div class="invoice-row"><span>Bill Number</span><strong>{{ $bill->bill_number }}</strong></div>
                <div class="invoice-row"><span>Invoice Number</span><strong>{{ $bill->invoice_number }}</strong></div>
                <div class="invoice-row"><span>Receipt Number</span><strong>{{ $bill->receipt_number }}</strong></div>
                <div class="invoice-row"><span>Date</span><strong>{{ $bill->posted_at->format('M d, Y h:i A') }}</strong></div>
                <div class="invoice-row"><span>Mode of Payment</span><strong>{{ $bill->method_label }}</strong></div>
                <div class="invoice-row"><span>Reference</span><strong>{{ $bill->reference_number ?: '—' }}</strong></div>
                <div class="invoice-row"><span>Received By</span><strong>{{ $bill->receivedBy->name ?? '—' }}</strong></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th class="num">Unit</th>
                    <th class="num">Unit Price</th>
                    <th class="num">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->items as $item)
                    <tr>
                        <td>
                            {{ $item->service_name }}
                            @if($item->service_code)
                                <br><small style="color:#757575;">{{ $item->service_code }}</small>
                            @endif
                        </td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="num">${{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="num">Mode of Payment</td>
                    <td class="num">{{ $bill->method_label }}</td>
                </tr>
                <tr class="invoice-total">
                    <td colspan="3" class="num"><strong>Total Bill Amount</strong></td>
                    <td class="num"><strong>${{ number_format($bill->total_amount, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 300);
        });
    </script>
</body>
</html>
