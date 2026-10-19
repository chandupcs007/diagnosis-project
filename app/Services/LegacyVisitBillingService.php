<?php

namespace App\Services;

use App\Models\Visit;
use App\Models\VisitBillingFinal;
use App\Models\VisitBillingItemDetail;
use App\Models\VisitBillingPayment;
use App\Models\VisitBillingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LegacyVisitBillingService
{
    public function __construct(
        protected PaymentSequenceService $paymentSequenceService
    ) {}

    /**
     * @param  Collection<int, VisitBillingService>  $services
     * @return array{bill_no: int, payment_receipt_no: string, final_id: string}
     */
    public function recordCollectedPayment(
        Visit $visit,
        Collection $services,
        float $total,
        string $paymentMethod,
        ?string $referenceNumber,
        ?string $notes
    ): array {
        return DB::transaction(function () use ($visit, $services, $total, $paymentMethod, $referenceNumber, $notes) {
            $billNo = $this->paymentSequenceService->nextBillNo();
            $receiptNo = $this->paymentSequenceService->nextReceiptNumber();
            $finalId = $this->paymentSequenceService->nextFinalId();

            $encounterNr = $visit->legacyEncounterNr();
            $userName = Auth::user()?->name ?? 'system';
            $now = now();
            $today = $now->toDateString();
            $typeNr = (int) config('diagnosis.billing.default_type_nr', 925);
            $doctorNr = (int) ($visit->doctor_id ?? 0);

            foreach ($services as $service) {
                VisitBillingItemDetail::create([
                'bill_item_encounter_nr' => $encounterNr,
                'bill_item_code' => $service->service_code,
                'bill_item_desc' => $service->service_name,
                'bill_item_unit_cost' => round((float) $service->unit_price, 2),
                'bill_item_units' => (float) $service->quantity,
                'bill_item_amount' => round((float) $service->total_amount, 2),
                'bill_item_date' => $today,
                'request_date' => $today,
                'bill_item_status' => 0,
                'bill_no' => $billNo,
                'bill_request_nr' => '',
                'type_nr' => $typeNr,
                'sort_nr' => 70,
                'insurance_nr' => '0',
                'insurance_firm_id' => '0',
                'insurance_class_nr' => 0,
                'bill_item_final_amount' => 0,
                'doctor_nr' => $doctorNr,
                'status' => '',
                'modify_id' => $userName,
                'modify_time' => $now,
                'create_id' => $userName,
                'create_time' => $now,
                'sub_item' => 0,
                'doctor_payment' => 0,
                'bill_item_exclusion' => 0,
                'history' => $notes ?? '',
                'ex_units' => 0,
                'bill_item_exe' => 0,
                'ex_item' => 0,
                'inc_item' => 0,
                'ex_dis' => 0,
                'auto_final' => 0,
                ]);
            }

            $paymentPayload = $this->paymentAmountFields($paymentMethod, $total, $referenceNumber);

            VisitBillingPayment::create(array_merge([
            'payment_encounter_nr' => $encounterNr,
            'payment_receipt_no' => $receiptNo,
            'bill_no' => $billNo,
            'payment_date' => $today,
            'payment_time' => $now->format('H:i:s'),
            'payment_amount_total' => $total,
            'final_settlement' => 1,
            'status' => 'paid',
            'history' => 'Create: ' . $now->format('Y-m-d H:i:s') . ' = ' . $userName,
            'modify_id' => $userName,
            'modify_time' => $now,
            'create_id' => $userName,
            'create_time' => $now,
            'auto_final' => 0,
            ], $paymentPayload));

            VisitBillingFinal::create([
            'final_id' => $finalId,
            'final_encounter_nr' => $encounterNr,
            'bill_no' => $billNo,
            'final_date' => $today,
            'final_time' => $now->format('H:i:s'),
            'final_total_bill_amount' => $total,
            'final_discount' => 0,
            'exclusion_discount' => 0,
            'final_total_receipt_amount' => $total,
            'final_amount_due' => 0,
            'final_amount_recieved' => $total,
            'final_sub_amt' => 0,
            'received_from_donor' => '',
            'isprinted' => 1,
            'status' => '',
            'create_id' => $userName,
            'create_time' => $now,
            'fst' => 0,
            'navision' => 0,
            'round_up' => 0,
            'auto_final' => 0,
            ]);

            return [
                'bill_no' => $billNo,
                'payment_receipt_no' => $receiptNo,
                'final_id' => $finalId,
            ];
        });
    }

    protected function paymentAmountFields(string $method, float $total, ?string $reference): array
    {
        $reference = $reference ?: '0';

        return match ($method) {
            'cash' => [
                'payment_cash_amount' => $total,
                'payment_cheque_no' => '0',
                'payment_cheque_amount' => 0,
                'payment_creditcard_no' => '0',
                'payment_creditcard_amount' => 0,
                'payment_mtn_no' => '0',
                'payment_mtn_amt' => 0,
                'payment_airtel_no' => '0',
                'payment_airtel_amt' => 0,
                'payment_ecs_type' => '',
            ],
            'card' => [
                'payment_cash_amount' => 0,
                'payment_cheque_no' => '0',
                'payment_cheque_amount' => 0,
                'payment_creditcard_no' => $reference,
                'payment_creditcard_amount' => $total,
                'payment_mtn_no' => '0',
                'payment_mtn_amt' => 0,
                'payment_airtel_no' => '0',
                'payment_airtel_amt' => 0,
                'payment_ecs_type' => '',
            ],
            'cheque' => [
                'payment_cash_amount' => 0,
                'payment_cheque_no' => $reference,
                'payment_cheque_amount' => $total,
                'payment_creditcard_no' => '0',
                'payment_creditcard_amount' => 0,
                'payment_mtn_no' => '0',
                'payment_mtn_amt' => 0,
                'payment_airtel_no' => '0',
                'payment_airtel_amt' => 0,
                'payment_ecs_type' => '',
            ],
            'upi' => [
                'payment_cash_amount' => 0,
                'payment_cheque_no' => '0',
                'payment_cheque_amount' => 0,
                'payment_creditcard_no' => '0',
                'payment_creditcard_amount' => 0,
                'payment_mtn_no' => $reference,
                'payment_mtn_amt' => $total,
                'payment_mtn_bank' => config('diagnosis.billing.mtn_bank_label', 'Mtn Main'),
                'payment_airtel_no' => '0',
                'payment_airtel_amt' => 0,
                'payment_ecs_type' => 'Mtn Main',
            ],
            default => [
                'payment_cash_amount' => 0,
                'payment_cheque_no' => '0',
                'payment_cheque_amount' => 0,
                'payment_creditcard_no' => '0',
                'payment_creditcard_amount' => 0,
                'payment_mtn_no' => '0',
                'payment_mtn_amt' => 0,
                'payment_airtel_no' => '0',
                'payment_airtel_amt' => 0,
                'payment_ecs_type' => '',
            ],
        };
    }
}
