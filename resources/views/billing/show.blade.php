@extends('layouts.app')

@section('title', 'Billing — ' . $visit->patient->full_name)

@push('page-header')
@include('layouts.partials.page-header', [
    'title' => 'Patient Billing',
    'breadcrumb' => 'Billing / ' . ($section === 'previous' ? 'Previous Patients' : 'Current Patients'),
    'actions' => '<div class="d-flex gap-2">'
        . '<form method="POST" action="' . route('billing.status.update', $visit) . '">'
        . csrf_field()
        . method_field('PUT')
        . ($section === 'previous'
            ? '<input type="hidden" name="status" value="scheduled"><button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-undo fa-sm me-1"></i> Reopen as Current</button>'
            : '<input type="hidden" name="status" value="completed"><button type="submit" class="btn btn-outline-secondary btn-sm"><i class="fas fa-check fa-sm me-1"></i> Move to Previous</button>')
        . '</form>'
        . '<a href="' . route('billing.patients', $section) . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left fa-sm me-1"></i> Back to Patients</a>'
        . '</div>',
])
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $patient = $visit->patient;
    $genderIcon = $patient->gender === 'male' ? 'mars' : ($patient->gender === 'female' ? 'venus' : 'genderless');
@endphp

<div class="sqa-form-card mb-4">
    <div class="sqa-section-header sqa-patient-header">
        <h2>Patient</h2>
        <a class="sqa-posted-bills-link sqa-posted-bills-link--header {{ $showPostedBills ? 'is-open' : '' }}"
           href="{{ route('billing.show', array_filter(['visit' => $visit->id, 'from' => $section, 'posted' => $showPostedBills ? null : 1])) }}">
            <span><i class="fas fa-file-invoice-dollar me-1"></i> Posted Bills</span>
            <strong>{{ $postedBillCount }} {{ Str::plural('bill', $postedBillCount) }} paid</strong>
        </a>
    </div>
    <div class="sqa-form-body py-3">
        <div class="row align-items-end">
            <div class="col-xl-2 col-md-4 mb-2">
                <small class="text-uppercase text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.6px;">Patient ID</small>
                <span class="sqa-patient-id">{{ $patient->display_id }}</span>
            </div>
            <div class="col-xl-2 col-md-4 mb-2">
                <small class="text-uppercase text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.6px;">Visit Number</small>
                <span><strong>{{ $visit->visit_number }}</strong></span>
            </div>
            <div class="col-xl-3 col-md-4 mb-2">
                <small class="text-uppercase text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.6px;">Patient Name</small>
                <span>{{ $patient->full_name }}</span>
            </div>
            <div class="col-xl-1 col-md-4 mb-2">
                <small class="text-uppercase text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.6px;">Gender</small>
                <span class="sqa-gender sqa-gender-{{ $patient->gender }}" title="{{ ucfirst($patient->gender) }}">
                    <i class="fas fa-{{ $genderIcon }}"></i>
                </span>
            </div>
            <div class="col-xl-1 col-md-4 mb-2">
                <small class="text-uppercase text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.6px;">Age</small>
                <span>{{ $patient->date_of_birth ? $patient->date_of_birth->age . ' yrs' : '—' }}</span>
            </div>
            <div class="col-xl-3 col-md-4 mb-2">
                <form method="POST" action="{{ route('billing.payer-type.update', $visit) }}" class="d-flex align-items-end gap-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="from" value="{{ $section }}">
                    <div class="flex-grow-1">
                        <label for="payer_type" class="text-uppercase text-muted d-block mb-1" style="font-size: 0.68rem; letter-spacing: 0.6px;">Patient Type</label>
                        <select id="payer_type" name="payer_type" class="form-control form-control-sm">
                            @foreach(config('diagnosis.payer_types') as $value => $label)
                                <option value="{{ $value }}" {{ $visit->payer_type === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm">Save</button>
                </form>
            </div>
        </div>

        @if($showPostedBills)
        <div class="sqa-posted-bills-panel">
            @if($visit->bills->count() > 0)
                <div class="table-responsive">
                    <table class="table sqa-data-table mb-0">
                        <thead>
                            <tr>
                                <th>Bill Number</th>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th class="text-end">Print</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visit->bills as $bill)
                                <tr class="{{ $selectedBill && $selectedBill->id === $bill->id ? 'table-active' : '' }}">
                                    <td>
                                        <a href="{{ route('billing.show', ['visit' => $visit->id, 'from' => $section, 'posted' => 1, 'bill' => $bill->id]) }}"
                                           class="fw-bold text-decoration-none">
                                            {{ $bill->bill_number }}
                                        </a>
                                    </td>
                                    <td>{{ $bill->invoice_number }}</td>
                                    <td>{{ $bill->posted_at->format('M d, Y h:i A') }}</td>
                                    <td>${{ number_format($bill->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('billing.bills.print', $bill) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-print me-1"></i> Print
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 text-center text-muted">No paid bills posted yet.</div>
            @endif

            @if($selectedBill)
                <div class="sqa-bill-detail">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">{{ $selectedBill->bill_number }}</h6>
                        <a href="{{ route('billing.bills.print', $selectedBill) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <small class="text-uppercase text-muted d-block">Invoice</small>
                            <strong>{{ $selectedBill->invoice_number }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-uppercase text-muted d-block">Receipt</small>
                            <strong>{{ $selectedBill->receipt_number }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-uppercase text-muted d-block">Mode of Payment</small>
                            <strong>{{ $selectedBill->method_label }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-uppercase text-muted d-block">Total Bill Amount</small>
                            <strong>${{ number_format($selectedBill->total_amount, 2) }}</strong>
                        </div>
                    </div>
                    @include('billing.partials.bill-lines', ['bill' => $selectedBill])
                </div>
            @endif
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="sqa-form-card h-100">
            <h2 class="sqa-section-header">Raise Service</h2>
            <div class="sqa-form-body">
                @include('billing.partials.raise-service-form', [
                    'visit' => $visit,
                    'formId' => 'billing-form',
                    'showRedirectFields' => true,
                    'section' => $section,
                ])
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="sqa-form-card h-100">
            <h2 class="sqa-section-header">Collect Payment</h2>
            <div class="sqa-form-body">
                @if($unbilledServices->count() > 0)
                    <form method="POST" action="{{ route('billing.payments.store', $visit) }}" id="collect-payment-form">
                        @csrf
                        <input type="hidden" name="from" value="{{ $section }}">
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between">
                                <span>Services on this bill <span class="text-danger">*</span></span>
                                <span id="collect-bill-total">${{ number_format($unbilledTotal, 2) }}</span>
                            </label>
                            @php
                                $selectedServiceIds = collect(old('service_ids', $unbilledServices->pluck('id')->all()))
                                    ->map(fn ($id) => (int) $id);
                            @endphp
                            @foreach($unbilledServices as $service)
                                <div class="form-check mb-1">
                                    <input class="form-check-input bill-service-check" type="checkbox" name="service_ids[]"
                                           value="{{ $service->id }}" id="service-{{ $service->id }}"
                                           data-amount="{{ $service->total_amount }}"
                                           {{ $selectedServiceIds->contains($service->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="service-{{ $service->id }}">
                                        {{ $service->service_name }}
                                        <span class="text-muted">· {{ $service->quantity }} × ${{ number_format($service->unit_price, 2) }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-control" required>
                                @foreach(config('diagnosis.payment_methods') as $value => $label)
                                    <option value="{{ $value }}" {{ old('payment_method', 'cash') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="Card or claim number" value="{{ old('reference_number') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" class="form-control" value="{{ old('notes') }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-hand-holding-usd me-1"></i> Collect Payment
                        </button>
                    </form>
                @else
                    <p class="text-muted mb-0">
                        @if($billedTotal <= 0)
                            Raise a service before collecting payment.
                        @else
                            Every raised service is already on a paid bill.
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm sqa-list-card mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold">Raised Services</h6>
    </div>
    <div class="card-body p-0">
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
                            <th>Bill</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visit->billingServices as $service)
                            <tr>
                                <td>{{ $service->created_at->format('M d, Y') }}</td>
                                <td><strong>{{ $service->service_name }}</strong></td>
                                <td>{{ $service->service_code ?? '—' }}</td>
                                <td>{{ $service->quantity }}</td>
                                <td>${{ number_format($service->unit_price, 2) }}</td>
                                <td><strong>${{ number_format($service->total_amount, 2) }}</strong></td>
                                <td>
                                    @if($service->bill)
                                        <a href="{{ route('billing.show', ['visit' => $visit->id, 'from' => $section, 'posted' => 1, 'bill' => $service->bill->id]) }}">
                                            {{ $service->bill->bill_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">Open</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end"><strong>Grand Total</strong></td>
                            <td><strong>${{ number_format($billedTotal, 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">No services raised yet.</div>
        @endif
    </div>
</div>

<div class="card shadow-sm sqa-list-card mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold">Payments</h6>
    </div>
    <div class="card-body p-0">
        @if($visit->payments->count() > 0)
            <div class="table-responsive">
                <table class="table sqa-data-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Received By</th>
                            <th>Bill</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visit->payments as $payment)
                            <tr>
                                <td>{{ $payment->paid_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $payment->method_label }}</td>
                                <td>{{ $payment->reference_number ?? '—' }}</td>
                                <td>{{ $payment->receivedBy->name ?? '—' }}</td>
                                <td>{{ $payment->bill->bill_number ?? '—' }}</td>
                                <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end"><strong>Total Collected</strong></td>
                            <td><strong>${{ number_format($paidTotal, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">No payments collected yet.</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const billTotal = document.getElementById('collect-bill-total');
    function updateCollectTotal() {
        if (!billTotal) {
            return;
        }
        let total = 0;
        document.querySelectorAll('.bill-service-check:checked').forEach(function (input) {
            total += parseFloat(input.dataset.amount || '0');
        });
        billTotal.textContent = '$' + total.toFixed(2);
    }
    document.querySelectorAll('.bill-service-check').forEach(function (input) {
        input.addEventListener('change', updateCollectTotal);
    });
    updateCollectTotal();
});
</script>
@endpush
