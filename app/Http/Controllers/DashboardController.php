<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Sample data - replace with actual data from your models
        $data = [
            'patientCount' => 150,
            'diagnosisToday' => 12,
            'pendingCases' => 8,
            'successRate' => 85,
        ];

        return view('dashboard', $data);
    }
}