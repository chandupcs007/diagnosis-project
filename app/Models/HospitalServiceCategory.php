<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalServiceCategory extends Model
{
    protected $table = 'hospital_service_category';

    protected $primaryKey = 'nr';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'nr',
        'category',
        'name',
        'LD_var',
        'code',
        'description',
        'multidept',
        'hide_from',
        'status',
        'history',
        'modify_id',
        'modify_time',
        'create_id',
        'create_time',
        'navision',
        'service_type',
    ];
}
