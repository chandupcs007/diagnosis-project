<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'pid',
        'temp_number',
        'registration_status',
        'pid_generated_at',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'zip_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_history',
        'photo_path',
        'fingerprint_hash',
        'fingerprint_captured_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'pid_generated_at' => 'datetime',
        'fingerprint_captured_at' => 'datetime',
    ];

    protected $hidden = [
        'fingerprint_hash',
    ];

    /**
     * Get the full name of the person
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the age of the person
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth->age;
    }

    /**
     * Visits associated with this patient.
     */
    public function visits()
    {
        return $this->hasMany(Visit::class, 'patient_id');
    }

    /**
     * Most recent visit for this patient.
     */
    public function latestVisit()
    {
        return $this->hasOne(Visit::class, 'patient_id')->latestOfMany();
    }

    /**
     * Whether this patient already has a visit on record.
     */
    public function hasVisit(): bool
    {
        if (isset($this->visits_count)) {
            return $this->visits_count > 0;
        }

        return $this->visits()->exists();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function isRegistered(): bool
    {
        return $this->registration_status === 'registered' && !empty($this->pid);
    }

    public function isTemp(): bool
    {
        return $this->registration_status === 'temp';
    }

    public function hasFingerprint(): bool
    {
        return !empty($this->fingerprint_hash);
    }

    public function getDisplayIdAttribute(): string
    {
        if ($this->pid) {
            return $this->pid;
        }

        if ($this->temp_number) {
            return $this->temp_number;
        }

        return '#' . $this->id;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return app(\App\Services\PatientBiometricService::class)->photoUrl($this->photo_path);
    }
}