<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyLabTest extends Model
{
    protected $table = 'lab_test';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];
}
