<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitBillingService extends Model
{
    protected $fillable = [
        'visit_id',
        'visit_bill_id',
        'service_name',
        'service_code',
        'quantity',
        'unit_price',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(VisitBill::class, 'visit_bill_id');
    }

    public function labRequest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LabRequest::class);
    }
}
