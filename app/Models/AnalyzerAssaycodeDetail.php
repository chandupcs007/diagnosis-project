<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalyzerAssaycodeDetail extends Model
{
    protected $table = 'analyzer_assaycode_details';

    protected $fillable = [
        'analyzer_id',
        'assay_code',
    ];

    public function analyzer(): BelongsTo
    {
        return $this->belongsTo(AnalyzerDetail::class, 'analyzer_id', 'analyzer_id');
    }

    public function hisMappings(): HasMany
    {
        return $this->hasMany(AnalyzerAssaycodeToHis::class, 'analyzer_assaycode_id');
    }
}
