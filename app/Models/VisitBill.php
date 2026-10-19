<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisitBill extends Model
{
    protected $fillable = [
        'visit_id',
        'bill_number',
        'invoice_number',
        'receipt_number',
        'payment_method',
        'total_amount',
        'reference_number',
        'notes',
        'received_by',
        'posted_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VisitBillItem::class)->orderBy('id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(VisitPayment::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return config('diagnosis.payment_methods.' . $this->payment_method, ucfirst((string) $this->payment_method));
    }

    public static function nextNumbers(): array
    {
        $date = now()->format('Ymd');
        $last = static::query()
            ->where('bill_number', 'like', 'BILL' . $date . '%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $sequence = $last ? ((int) substr($last->bill_number, -4)) + 1 : 1;
        $sequence = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        return [
            'bill_number' => 'BILL' . $date . $sequence,
            'invoice_number' => 'INV' . $date . $sequence,
            'receipt_number' => 'RCT' . $date . $sequence,
        ];
    }
}
