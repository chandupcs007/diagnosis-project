<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Services\PatientBiometricService;
use App\Services\PatientIdentifierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function __construct(
        protected PatientIdentifierService $patientIdentifierService,
        protected PatientBiometricService $patientBiometricService
    ) {}

    /**
     * Display a listing of the patients.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchType = $request->input('search_type', 'all');

        $patients = Person::when($search, function ($query, $search) use ($searchType) {
            return $query->where(function ($q) use ($search, $searchType) {
                switch ($searchType) {
                    case 'id':
                        $q->where('id', 'LIKE', "%{$search}%")
                          ->orWhere('pid', 'LIKE', "%{$search}%")
                          ->orWhere('temp_number', 'LIKE', "%{$search}%");
                        break;
                    case 'name':
                        $q->where('first_name', 'LIKE', "%{$search}%")
                          ->orWhere('last_name', 'LIKE', "%{$search}%")
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                        break;
                    default:
                        $q->where('id', 'LIKE', "%{$search}%")
                          ->orWhere('pid', 'LIKE', "%{$search}%")
                          ->orWhere('temp_number', 'LIKE', "%{$search}%")
                          ->orWhere('first_name', 'LIKE', "%{$search}%")
                          ->orWhere('last_name', 'LIKE', "%{$search}%")
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                          ->orWhere('phone', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%");
                }
            });
        })
        ->withCount('visits')
        ->with('latestVisit')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('patients.index', compact('patients', 'search', 'searchType'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $duplicateMatches = $this->patientBiometricService->findPotentialDuplicates($request->all());

        if (!empty($duplicateMatches)) {
            $primaryMatch = $duplicateMatches[0];

            return redirect()->back()
                ->withInput()
                ->with('duplicate_patient', $this->patientBiometricService->formatDuplicateMatch($primaryMatch))
                ->withErrors([
                    'duplicate' => $primaryMatch['message'],
                ]);
        }

        $patient = Person::create($request->except(['photo_data', 'fingerprint_template']));
        $this->patientIdentifierService->assignPid($patient);

        $this->persistBiometrics($patient, $request);

        return redirect()->route('patients.show', $patient->id)
            ->with('success', 'Patient registered successfully! PID: ' . $patient->fresh()->pid);
    }

    public function show($id)
    {
        $patient = Person::with('latestVisit')->findOrFail($id);

        return view('patients.show', [
            'patient' => $patient,
            'photoUrl' => $this->patientBiometricService->photoUrl($patient->photo_path),
        ]);
    }

    public function idCard(Person $patient)
    {
        return view('patients.id-card', [
            'patient' => $patient,
            'photoUrl' => $this->patientBiometricService->photoUrl($patient->photo_path),
        ]);
    }

    public function edit($id)
    {
        $patient = Person::findOrFail($id);

        return view('patients.edit', [
            'patient' => $patient,
            'photoUrl' => $this->patientBiometricService->photoUrl($patient->photo_path),
        ]);
    }

    public function update(Request $request, $id)
    {
        $patient = Person::findOrFail($id);

        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $duplicateMatches = $this->patientBiometricService->findPotentialDuplicates(
            $request->all(),
            $patient->id
        );

        if (!empty($duplicateMatches)) {
            $primaryMatch = $duplicateMatches[0];

            return redirect()->back()
                ->withInput()
                ->with('duplicate_patient', $this->patientBiometricService->formatDuplicateMatch($primaryMatch))
                ->withErrors([
                    'duplicate' => $primaryMatch['message'],
                ]);
        }

        $patient->update($request->except(['photo_data', 'fingerprint_template']));
        $this->persistBiometrics($patient, $request);

        return redirect()->route('patients.show', $patient->id)
            ->with('success', 'Patient updated successfully!');
    }

    public function destroy($id)
    {
        $patient = Person::findOrFail($id);
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully!');
    }

    public function checkDuplicate(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'fingerprint_template' => 'nullable|string',
            'exclude_patient_id' => 'nullable|integer|exists:persons,id',
        ]);

        $matches = $this->patientBiometricService->findPotentialDuplicates(
            $validated,
            $validated['exclude_patient_id'] ?? null
        );

        return response()->json([
            'duplicate' => !empty($matches),
            'matches' => array_map(
                fn ($match) => $this->patientBiometricService->formatDuplicateMatch($match),
                $matches
            ),
        ]);
    }

    public function searchByFingerprint(Request $request)
    {
        $validated = $request->validate([
            'fingerprint_template' => 'required|string',
        ]);

        $patient = $this->patientBiometricService->findByFingerprintTemplate(
            $validated['fingerprint_template']
        );

        if (!$patient) {
            return response()->json([
                'found' => false,
                'message' => 'No patient found with this fingerprint.',
            ], 404);
        }

        return response()->json([
            'found' => true,
            'message' => 'Patient matched successfully.',
            'patient' => [
                'id' => $patient->id,
                'pid' => $patient->display_id,
                'name' => $patient->full_name,
            ],
            'redirect_url' => route('patients.show', $patient->id),
        ]);
    }

    protected function validationRules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string',
            'photo_data' => 'nullable|string',
            'fingerprint_template' => 'nullable|string',
        ];
    }

    protected function persistBiometrics(Person $patient, Request $request): void
    {
        if ($request->filled('photo_data')) {
            try {
                $this->patientBiometricService->savePhotoFromBase64($patient, $request->input('photo_data'));
            } catch (\InvalidArgumentException $exception) {
                report($exception);
            }
        }

        if ($request->filled('fingerprint_template')) {
            $this->patientBiometricService->saveFingerprint(
                $patient,
                $request->input('fingerprint_template')
            );
        }
    }
}
