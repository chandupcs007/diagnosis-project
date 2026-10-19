<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\LegacyVisitBillingService;
use App\Services\MasterServiceCreationService;
use App\Models\VisitBill;
use App\Models\VisitPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function __construct(
        protected LegacyVisitBillingService $legacyVisitBillingService
    ) {}

    public function index()
    {
        $currentCount = Visit::currentForBilling()->count();
        $previousCount = Visit::previousForBilling()->count();

        return view('billing.index', compact('currentCount', 'previousCount'));
    }

    public function patients(Request $request, string $section)
    {
        abort_unless(in_array($section, ['current', 'previous'], true), 404);

        $search = trim((string) $request->query('search', ''));

        $visits = Visit::with('patient')
            ->when($section === 'current', fn ($query) => $query->currentForBilling(), fn ($query) => $query->previousForBilling())
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('visit_number', 'like', '%' . $search . '%')
                        ->orWhereHas('patient', function ($patient) use ($search) {
                            $patient->where('pid', 'like', '%' . $search . '%')
                                ->orWhere('temp_number', 'like', '%' . $search . '%')
                                ->orWhere('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByDesc('visit_date')
            ->orderByDesc('visit_time')
            ->get();

        return view('billing.patients', compact('visits', 'section', 'search'));
    }

    public function show(Visit $visit)
    {
        $visit->load([
            'patient',
            'doctor',
            'billingServices.labRequest',
            'billingServices.bill',
            'payments.receivedBy',
            'payments.bill',
            'bills.items',
            'bills.payment.receivedBy',
            'bills.receivedBy',
        ]);

        $masterCatalog = app(MasterServiceCreationService::class);
        $hospitalCategories = $masterCatalog->hospitalCategories();
        $labDepartments = config('diagnosis.lab_departments', []);
        $billingCatalog = $masterCatalog->billingCatalog();

        $billedTotal = round((float) $visit->billingServices->sum('total_amount'), 2);
        $paidTotal = round((float) $visit->payments->sum('amount'), 2);
        $unbilledServices = $visit->billingServices->whereNull('visit_bill_id')->values();
        $unbilledTotal = round((float) $unbilledServices->sum('total_amount'), 2);
        $postedBillCount = $visit->bills->count();

        $selectedBill = null;
        if (request()->filled('bill')) {
            $selectedBill = $visit->bills->firstWhere('id', (int) request('bill'));
        }

        $showPostedBills = request()->boolean('posted') || $selectedBill !== null;

        $section = request('from');
        if (! in_array($section, ['current', 'previous'], true)) {
            $section = $visit->isPreviousForBilling() ? 'previous' : 'current';
        }

        return view('billing.show', compact(
            'visit',
            'hospitalCategories',
            'labDepartments',
            'billingCatalog',
            'billedTotal',
            'paidTotal',
            'unbilledServices',
            'unbilledTotal',
            'postedBillCount',
            'selectedBill',
            'showPostedBills',
            'section'
        ));
    }

    public function printBill(VisitBill $bill)
    {
        $bill->load([
            'items',
            'payment.receivedBy',
            'receivedBy',
            'visit.patient',
            'visit.doctor',
        ]);

        return view('billing.print', compact('bill'));
    }

    public function updateStatus(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed',
        ]);

        $visit->update(['status' => $validated['status']]);

        $section = $validated['status'] === 'completed' ? 'previous' : 'current';
        $message = $validated['status'] === 'completed'
            ? 'Visit moved to Previous Patients.'
            : 'Visit reopened under Current Patients.';

        return redirect()
            ->route('billing.show', ['visit' => $visit, 'from' => $section])
            ->with('success', $message);
    }

    public function updatePayerType(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'payer_type' => 'required|in:self_pay,insurance',
        ]);

        $visit->update($validated);

        return redirect()
            ->route('billing.show', ['visit' => $visit, 'from' => $request->input('from')])
            ->with('success', 'Patient type updated.');
    }

    public function storePayment(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'integer',
            'payment_method' => 'required|in:cash,card,upi,cheque,insurance',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $services = $visit->billingServices()
            ->whereNull('visit_bill_id')
            ->whereIn('id', $validated['service_ids'])
            ->orderBy('id')
            ->get();

        if ($services->count() !== count(array_unique($validated['service_ids']))) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'service_ids' => 'Select services that have not already been posted on a bill.',
                ]);
        }

        $total = round((float) $services->sum('total_amount'), 2);

        $legacyMeta = [];

        $bill = DB::transaction(function () use ($visit, $validated, $services, $total, &$legacyMeta) {
            $legacyMeta = $this->legacyVisitBillingService->recordCollectedPayment(
                $visit,
                $services,
                $total,
                $validated['payment_method'],
                $validated['reference_number'] ?? null,
                $validated['notes'] ?? null
            );

            $numbers = VisitBill::nextNumbers();

            $bill = VisitBill::create([
                'visit_id' => $visit->id,
                'bill_number' => $numbers['bill_number'],
                'invoice_number' => $numbers['invoice_number'],
                'receipt_number' => $numbers['receipt_number'],
                'payment_method' => $validated['payment_method'],
                'total_amount' => $total,
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'received_by' => Auth::id(),
                'posted_at' => now(),
            ]);

            foreach ($services as $service) {
                $bill->items()->create([
                    'visit_billing_service_id' => $service->id,
                    'service_name' => $service->service_name,
                    'service_code' => $service->service_code,
                    'quantity' => $service->quantity,
                    'unit_price' => $service->unit_price,
                    'total_amount' => $service->total_amount,
                ]);

                $service->update(['visit_bill_id' => $bill->id]);
            }

            VisitPayment::create([
                'visit_id' => $visit->id,
                'visit_bill_id' => $bill->id,
                'amount' => $total,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'received_by' => Auth::id(),
                'paid_at' => now(),
            ]);

            return $bill;
        });

        return redirect()
            ->route('billing.show', [
                'visit' => $visit,
                'from' => $request->input('from'),
                'posted' => 1,
                'bill' => $bill->id,
            ])
            ->with('success', 'Bill ' . $bill->bill_number . ' posted. Legacy bill no: '
                . ($legacyMeta['bill_no'] ?? '—')
                . ', receipt: ' . ($legacyMeta['payment_receipt_no'] ?? '—')
                . ', final: ' . ($legacyMeta['final_id'] ?? '—'));
    }
}
