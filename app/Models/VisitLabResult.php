<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitLabResult extends Model
{
    protected $fillable = [
        'visit_id',
        'test_name',
        'test_code',
        'result_value',
        'unit',
        'reference_range',
        'status',
        'notes',
        'tested_at',
    ];

    protected $casts = [
        'tested_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
