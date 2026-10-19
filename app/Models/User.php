<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'photo_path',
        'password',
        'role',
        'department',
        'specialization',
        'phone',
        'address',
        'qualification',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship with visits as doctor
     */
    public function visits()
    {
        return $this->hasMany(Visit::class, 'doctor_id');
    }

    /**
     * Check if user is a doctor
     */
    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Scope to get only doctors
     */
    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return app(\App\Services\UserPhotoService::class)->photoUrl($this->photo_path);
    }

    public function getDisplayDepartmentAttribute(): string
    {
        if ($this->department) {
            return $this->department;
        }

        if ($this->specialization) {
            return $this->specialization;
        }

        return ucfirst($this->role ?? 'User');
    }
}