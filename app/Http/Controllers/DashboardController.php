<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\LabRequest;
use App\Models\Person;
use App\Models\Visit;
use App\Models\VisitDiagnosis;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $patientCount = Person::count();
        $visitsToday = Visit::whereDate('visit_date', $today)->count();
        $diagnosisToday = VisitDiagnosis::whereDate('created_at', $today)->count();

        $pendingLabRequests = LabRequest::where('status', '!=', LabRequest::STATUS_REPORT_DATA)->count();
        $pendingAppointments = Appointment::whereIn('status', ['scheduled', 'checked_in'])
            ->whereDate('appointment_date', '>=', $today)
            ->count();
        $pendingCases = $pendingLabRequests + $pendingAppointments;

        $totalLabRequests = LabRequest::count();
        $completedLabRequests = LabRequest::where('status', LabRequest::STATUS_REPORT_DATA)->count();
        $successRate = $totalLabRequests > 0
            ? (int) round(($completedLabRequests / $totalLabRequests) * 100)
            : 0;

        $recentPatients = Person::latest()->limit(5)->get();
        $recentLabRequests = LabRequest::with(['visit.patient'])
            ->where('status', '!=', LabRequest::STATUS_REPORT_DATA)
            ->latest()
            ->limit(5)
            ->get();
        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)
            ->orderBy('slot_time')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'patientCount',
            'visitsToday',
            'diagnosisToday',
            'pendingCases',
            'pendingLabRequests',
            'pendingAppointments',
            'successRate',
            'recentPatients',
            'recentLabRequests',
            'todayAppointments'
        ));
    }
}
