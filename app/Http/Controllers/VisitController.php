<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Person;
use App\Models\User;
use App\Services\MasterServiceCreationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitController extends Controller
{
    /**
     * Show the form for creating a new visit.
     */
 public function create($patientId)
{
    $patient = Person::withCount('visits')->findOrFail($patientId);

    if ($patient->hasVisit()) {
        return redirect()->route('patients.index', request()->only('search'))
            ->with('error', 'A visit has already been created for this patient.');
    }

    // Get only users with doctor role
    $doctors = User::where('role', 'doctor')->get();
    
    $departments = config('diagnosis.departments');

    return view('visits.create', compact('patient', 'doctors', 'departments'));
}

    /**
     * Store a newly created visit in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:persons,id',
            'doctor_id' => 'required|exists:users,id',
            'department' => 'required|string|max:255',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'prescription' => 'nullable|string',
            'visit_date' => 'required|date',
            'visit_time' => 'required|date_format:H:i',
            'payer_type' => 'required|in:self_pay,insurance',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (Visit::where('patient_id', $request->patient_id)->exists()) {
            return redirect()->route('patients.index')
                ->with('error', 'A visit has already been created for this patient.');
        }

        // Generate unique visit number
        $visitNumber = Visit::generateVisitNumber();

        $visit = Visit::create([
            'visit_number' => $visitNumber,
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'department' => $request->department,
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'prescription' => $request->prescription,
            'visit_date' => $request->visit_date,
            'visit_time' => $request->visit_time,
            'status' => 'scheduled',
            'payer_type' => $request->payer_type,
        ]);

        return redirect()->route('visits.show', $visit->id)
            ->with('success', 'Visit created successfully! Visit Number: ' . $visitNumber);
    }

    /**
     * Display the specified visit.
     */
    public function show($id)
    {
        $visit = Visit::with([
            'patient',
            'doctor',
            'vitals.savedBy',
            'diagnoses',
            'chiefComplaints',
            'histories',
            'billingServices.labRequest',
            'labResults',
            'radiologyResults',
            'prescriptions.doctor',
        ])->findOrFail($id);

        $activeTab = request('tab', 'vitals');
        $masterCatalog = app(MasterServiceCreationService::class);
        $hospitalCategories = $masterCatalog->hospitalCategories();
        $labDepartments = config('diagnosis.lab_departments', []);
        $billingCatalog = $masterCatalog->billingCatalog();

        $previousVisits = Visit::with([
            'doctor',
            'vitals.savedBy',
            'diagnoses',
            'chiefComplaints',
            'histories',
            'billingServices',
            'labResults',
            'radiologyResults',
            'prescriptions.doctor',
        ])
            ->where('patient_id', $visit->patient_id)
            ->where('id', '!=', $visit->id)
            ->orderByDesc('visit_date')
            ->orderByDesc('visit_time')
            ->get();

        return view('visits.show', compact(
            'visit',
            'activeTab',
            'hospitalCategories',
            'labDepartments',
            'billingCatalog',
            'previousVisits'
        ));
    }

    /**
     * List all visits for a patient
     */
    public function patientVisits($patientId)
    {
        $patient = Person::findOrFail($patientId);
        $visits = Visit::with('doctor')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('visits.patient-index', compact('patient', 'visits'));
    }
}