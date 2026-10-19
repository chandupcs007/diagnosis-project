<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitBillingItemDetail extends Model
{
    protected $table = 'visit_billing_item_details';

    protected $primaryKey = 'bill_item_id';

    public $timestamps = false;

    protected $guarded = [];
}
