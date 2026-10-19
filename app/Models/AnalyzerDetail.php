<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalyzerDetail extends Model
{
    protected $table = 'analyzer_details';

    protected $fillable = [
        'analyzer_name',
        'analyzer_id',
    ];

    public function assayCodes(): HasMany
    {
        return $this->hasMany(AnalyzerAssaycodeDetail::class, 'analyzer_id', 'analyzer_id');
    }
}
