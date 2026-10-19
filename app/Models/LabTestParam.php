<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabTestParam extends Model
{
    protected $table = 'lab_test_param';

    protected $primaryKey = 'item_code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'item_code',
        'is_profile',
    ];

    protected $casts = [
        'is_profile' => 'integer',
    ];

    public function profileChildren(): HasMany
    {
        return $this->hasMany(LabTestProfileChild::class, 'parent_item_code', 'item_code')
            ->orderBy('sort_nr');
    }
}
