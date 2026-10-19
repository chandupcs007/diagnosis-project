<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncounterVitalsDetail extends Model
{
    protected $table = 'encounter_vitals_details';

    protected $fillable = [
        'visit_id',
        'vitals_details',
        'recorded_at',
        'saved_by',
    ];

    protected $casts = [
        'vitals_details' => 'array',
        'recorded_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function savedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saved_by');
    }

    public function detail(string $key, mixed $default = null): mixed
    {
        return data_get($this->vitals_details, $key, $default);
    }

    public function getBloodPressureAttribute(): ?string
    {
        return $this->detail('blood_pressure');
    }

    public function getPulseAttribute(): ?int
    {
        $value = $this->detail('pulse');

        return $value !== null ? (int) $value : null;
    }

    public function getTemperatureAttribute(): ?float
    {
        $value = $this->detail('temperature');

        return $value !== null ? (float) $value : null;
    }

    public function getRespiratoryRateAttribute(): ?int
    {
        $value = $this->detail('respiratory_rate');

        return $value !== null ? (int) $value : null;
    }

    public function getOxygenSaturationAttribute(): ?float
    {
        $value = $this->detail('oxygen_saturation');

        return $value !== null ? (float) $value : null;
    }

    public function getWeightAttribute(): ?float
    {
        $value = $this->detail('weight');

        return $value !== null ? (float) $value : null;
    }

    public function getHeightAttribute(): ?float
    {
        $value = $this->detail('height');

        return $value !== null ? (float) $value : null;
    }

    public function getNotesAttribute(): ?string
    {
        return $this->detail('notes');
    }
}
