<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitBillItem extends Model
{
    protected $fillable = [
        'visit_bill_id',
        'visit_billing_service_id',
        'service_name',
        'service_code',
        'quantity',
        'unit_price',
        'total_amount',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(VisitBill::class, 'visit_bill_id');
    }

    public function billingService(): BelongsTo
    {
        return $this->belongsTo(VisitBillingService::class, 'visit_billing_service_id');
    }
}
