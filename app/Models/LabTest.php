<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabTest extends Model
{
    protected $fillable = [
        'name',
        'code',
        'department',
        'sample_type',
        'unit_price',
        'unit',
        'reference_range',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function labRequests(): HasMany
    {
        return $this->hasMany(LabRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDepartmentLabelAttribute(): string
    {
        return config("diagnosis.lab_departments.{$this->department}.name", ucfirst($this->department));
    }
}
