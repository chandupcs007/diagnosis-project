<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingMaster extends Model
{
    protected $table = 'billing_master';

    protected $primaryKey = 'item_code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];
}
