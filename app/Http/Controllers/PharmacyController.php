<?php

namespace App\Http\Controllers;

class PharmacyController extends Controller
{
    public function sales()
    {
        return view('pharmacy.sales.index');
    }

    public function items()
    {
        return view('pharmacy.items.index');
    }

    public function po()
    {
        return view('pharmacy.po.index');
    }

    public function grn()
    {
        return view('pharmacy.grn.index');
    }
}
