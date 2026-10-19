<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyzerAssaycodeToHis extends Model
{
    protected $table = 'analyzer_assaycode_to_his';

    protected $fillable = [
        'analyzer_id',
        'analyzer_assaycode_id',
        'his_test_code',
    ];

    public function analyzer(): BelongsTo
    {
        return $this->belongsTo(AnalyzerDetail::class, 'analyzer_id', 'analyzer_id');
    }

    public function analyzerAssaycode(): BelongsTo
    {
        return $this->belongsTo(AnalyzerAssaycodeDetail::class, 'analyzer_assaycode_id');
    }

    public function labTest(): BelongsTo
    {
        return $this->belongsTo(LegacyLabTest::class, 'his_test_code', 'id');
    }
}
