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
}