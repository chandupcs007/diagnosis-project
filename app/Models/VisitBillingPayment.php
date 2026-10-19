<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitBillingPayment extends Model
{
    protected $table = 'visit_billing_payment';

    protected $primaryKey = 'payment_id';

    public $timestamps = false;

    protected $guarded = [];
}
