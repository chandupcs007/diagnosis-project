<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitDiagnosis extends Model
{
    protected $fillable = [
        'visit_id',
        'diagnosis_code',
        'diagnosis_name',
        'severity',
        'notes',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
