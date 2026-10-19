<?php

namespace App\Services;

use App\Models\PaymentType;
use Illuminate\Support\Facades\DB;

class PaymentSequenceService
{
    /**
     * Increment and return the next sequence for a payment_type row.
     * Must run inside an open DB transaction (uses lockForUpdate).
     */
    public function nextSequenceNumber(int $type): int
    {
        if (DB::transactionLevel() === 0) {
            throw new \RuntimeException('Payment sequences must be reserved inside a database transaction.');
        }

        $row = PaymentType::query()
            ->where('type', $type)
            ->lockForUpdate()
            ->firstOrFail();

        $row->no = (int) $row->no + 1;
        $row->save();

        return (int) $row->no;
    }

    public function nextBillNo(): int
    {
        $type = (int) config('diagnosis.billing.sequences.bill_no', 38);

        return $this->nextSequenceNumber($type);
    }

    public function nextReceiptNumber(): string
    {
        $type = (int) config('diagnosis.billing.sequences.op_receipt', 4);
        $prefix = config('diagnosis.billing.receipt_prefix', 'NHL-REC');
        $number = $this->nextSequenceNumber($type);
        $now = now();

        return sprintf('%s/%s/%s/%d', $prefix, $now->format('Y'), $now->format('m'), $number);
    }

    public function nextFinalId(): string
    {
        $type = (int) config('diagnosis.billing.sequences.op_final', 3);
        $prefix = config('diagnosis.billing.final_prefix', 'OPC');
        $number = $this->nextSequenceNumber($type);
        $now = now();

        return sprintf('%s/%s/%s/%d', $prefix, $now->format('Y'), $now->format('m'), $number);
    }
}
