<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'appointment_number',
        'patient_id',
        'doctor_id',
        'department',
        'appointment_date',
        'slot_time',
        'slot_duration_minutes',
        'status',
        'notes',
        'checked_in_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'checked_in_at' => 'datetime',
    ];

    public static function generateAppointmentNumber(): string
    {
        $prefix = 'APT';
        $date = now()->format('Ymd');
        $last = self::where('appointment_number', 'like', $prefix . $date . '%')->latest()->first();
        $sequence = $last ? (int) substr($last->appointment_number, -4) + 1 : 1;

        return $prefix . $date . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function getSlotEndTimeAttribute(): string
    {
        return \Carbon\Carbon::parse($this->slot_time)
            ->addMinutes($this->slot_duration_minutes)
            ->format('H:i');
    }
}
