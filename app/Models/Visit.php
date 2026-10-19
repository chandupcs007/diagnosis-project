<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_number',
        'patient_id',
        'doctor_id',
        'department',
        'symptoms',
        'diagnosis',
        'prescription',
        'status',
        'payer_type',
        'visit_date',
        'visit_time'
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    /**
     * Generate unique visit number
     */
    public static function generateVisitNumber()
    {
        $prefix = 'VIS';
        $date = now()->format('Ymd');
        $lastVisit = self::where('visit_number', 'like', $prefix . $date . '%')->latest()->first();
        
        $sequence = $lastVisit ? (int)substr($lastVisit->visit_number, -4) + 1 : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationship with Patient
     */
    public function patient()
    {
        return $this->belongsTo(Person::class, 'patient_id');
    }

    /**
     * Relationship with Doctor
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function vitals()
    {
        return $this->hasMany(EncounterVitalsDetail::class, 'visit_id')->orderByDesc('recorded_at');
    }

    public function diagnoses()
    {
        return $this->hasMany(VisitDiagnosis::class)->orderByDesc('created_at');
    }

    public function chiefComplaints()
    {
        return $this->hasMany(VisitChiefComplaint::class)->orderByDesc('created_at');
    }

    public function histories()
    {
        return $this->hasMany(VisitHistory::class)->orderByDesc('created_at');
    }

    public function billingServices()
    {
        return $this->hasMany(VisitBillingService::class)->orderByDesc('created_at');
    }

    public function labResults()
    {
        return $this->hasMany(VisitLabResult::class)->orderByDesc('tested_at');
    }

    public function radiologyResults()
    {
        return $this->hasMany(VisitRadiologyResult::class)->orderByDesc('reported_at');
    }

    public function prescriptions()
    {
        return $this->hasMany(VisitPrescription::class)->orderByDesc('created_at');
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class)->orderByDesc('created_at');
    }

    public function payments()
    {
        return $this->hasMany(VisitPayment::class)->orderByDesc('paid_at');
    }

    public function bills()
    {
        return $this->hasMany(VisitBill::class)->orderByDesc('posted_at');
    }

    public function scopeCurrentForBilling($query)
    {
        return $query->whereIn('status', ['scheduled', 'in_progress']);
    }

    public function scopePreviousForBilling($query)
    {
        return $query->whereIn('status', ['completed', 'cancelled']);
    }

    public function isPreviousForBilling(): bool
    {
        return in_array($this->status, ['completed', 'cancelled'], true);
    }

    public function getPayerTypeLabelAttribute(): string
    {
        return config('diagnosis.payer_types.' . $this->payer_type, 'Self Pay');
    }

    public function legacyEncounterNr(): int
    {
        $locationId = (int) config('diagnosis.billing.location_id', 10);

        return (int) ($locationId . str_pad((string) $this->id, 10, '0', STR_PAD_LEFT));
    }
}