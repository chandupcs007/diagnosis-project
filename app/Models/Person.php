<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'zip_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_history',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
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
}