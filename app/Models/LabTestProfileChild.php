<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabTestProfileChild extends Model
{
    protected $table = 'lab_test_profile_child';

    public $timestamps = false;

    protected $fillable = [
        'parent_item_code',
        'child_item_code',
        'sort_nr',
    ];

    public function parentTest(): BelongsTo
    {
        return $this->belongsTo(LegacyLabTest::class, 'parent_item_code', 'id');
    }

    public function childTest(): BelongsTo
    {
        return $this->belongsTo(LegacyLabTest::class, 'child_item_code', 'id');
    }
}
