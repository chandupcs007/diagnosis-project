<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HospitalSetting extends Model
{
    protected $fillable = [
        'hospital_name',
        'department_name',
        'contact_phone',
        'contact_email',
        'address',
        'website',
        'return_message',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path || !Storage::disk('public')->exists($this->logo_path)) {
            return null;
        }

        return '/storage/' . ltrim($this->logo_path, '/');
    }
}
