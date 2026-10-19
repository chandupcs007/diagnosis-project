<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalService extends Model
{
    protected $table = 'hospital_service';

    public $timestamps = false;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'category',
        'description',
        'inclusive',
        'exclusive',
        'notes',
        'std_code',
        'sub_level',
        'remarks',
        'navision',
        'work_type',
    ];
}
