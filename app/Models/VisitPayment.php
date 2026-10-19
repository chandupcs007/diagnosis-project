<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitPayment extends Model
{
    protected $fillable = [
        'visit_id',
        'visit_bill_id',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'received_by',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(VisitBill::class, 'visit_bill_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return config('diagnosis.payment_methods.' . $this->payment_method, ucfirst($this->payment_method));
    }
}
