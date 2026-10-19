<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Person;
use App\Services\PatientIdentifierService;
use App\Services\SlotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function __construct(
        protected SlotService $slotService,
        protected PatientIdentifierService $patientIdentifierService
    ) {}

    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor'])
            ->orderByDesc('appointment_date')
            ->orderBy('slot_time');

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->paginate(20)->withQueryString();

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $departments = config('diagnosis.departments');

        return view('appointments.create', compact('departments'));
    }

    public function doctors(Request $request)
    {
        $validated = $request->validate([
            'department' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $doctors = $this->slotService->getDoctorsForDepartment(
            $validated['department'],
            $validated['date']
        );

        return response()->json([
            'doctors' => $doctors->map(function ($doctor) {
                $schedule = $doctor->schedules->first();

                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialization' => $doctor->specialization,
                    'qualification' => $doctor->qualification,
                    'slot_duration_minutes' => $schedule?->slot_duration_minutes ?? config('diagnosis.default_slot_duration'),
                    'schedule' => $schedule ? [
                        'start' => substr($schedule->start_time, 0, 5),
                        'end' => substr($schedule->end_time, 0, 5),
                    ] : null,
                ];
            }),
        ]);
    }

    public function slots(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $slots = $this->slotService->getAvailableSlots(
            (int) $validated['doctor_id'],
            $validated['date']
        );

        return response()->json(['slots' => $slots]);
    }

    public function lookupPatient(Request $request)
    {
        $validated = $request->validate([
            'pid' => 'required|string',
        ]);

        $patient = $this->patientIdentifierService->findByPid($validated['pid']);

        if (!$patient) {
            return response()->json(['found' => false, 'message' => 'No registered patient found with this PID.'], 404);
        }

        return response()->json([
            'found' => true,
            'patient' => [
                'id' => $patient->id,
                'pid' => $patient->pid,
                'name' => $patient->full_name,
                'phone' => $patient->phone,
                'gender' => $patient->gender,
                'age' => $patient->age,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'doctor_id' => 'required|exists:users,id',
            'department' => 'required|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'slot_time' => 'required|date_format:H:i',
            'patient_type' => 'required|in:existing,new',
            'notes' => 'nullable|string',
        ];

        if ($request->patient_type === 'existing') {
            $rules['pid'] = 'required|string';
        } else {
            $rules['first_name'] = 'required|string|max:255';
            $rules['last_name'] = 'required|string|max:255';
            $rules['date_of_birth'] = 'required|date|before:today';
            $rules['gender'] = 'required|in:male,female,other';
            $rules['phone'] = 'required|string|max:20';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $slots = $this->slotService->getAvailableSlots(
            (int) $request->doctor_id,
            $request->appointment_date
        );

        $selectedSlot = collect($slots)->firstWhere('time', $request->slot_time);

        if (!$selectedSlot || !$selectedSlot['available']) {
            return redirect()->back()
                ->withErrors(['slot_time' => 'Selected slot is no longer available.'])
                ->withInput();
        }

        if ($request->patient_type === 'existing') {
            $patient = $this->patientIdentifierService->findByPid($request->pid);

            if (!$patient) {
                return redirect()->back()
                    ->withErrors(['pid' => 'Patient not found with the provided PID.'])
                    ->withInput();
            }
        } else {
            $patient = $this->patientIdentifierService->createTempPatient(
                $request->only(['first_name', 'last_name', 'date_of_birth', 'gender', 'phone'])
            );
        }

        $appointment = Appointment::create([
            'appointment_number' => Appointment::generateAppointmentNumber(),
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'department' => $request->department,
            'appointment_date' => $request->appointment_date,
            'slot_time' => $request->slot_time,
            'slot_duration_minutes' => $selectedSlot['duration_minutes'],
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        $message = 'Appointment booked successfully! Reference: ' . $appointment->appointment_number;

        if ($patient->isTemp()) {
            $message .= ' | Temp Number: ' . $patient->temp_number . ' (share with patient for check-in)';
        }

        return redirect()->route('appointments.show', $appointment)
            ->with('success', $message);
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);

        return view('appointments.show', compact('appointment'));
    }

    public function checkInForm()
    {
        return view('appointments.check-in');
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'temp_number' => 'required|string',
        ]);

        $patient = $this->patientIdentifierService->findByTempNumber($validated['temp_number']);

        if (!$patient) {
            return redirect()->back()
                ->withErrors(['temp_number' => 'No pending patient found with this temp number.'])
                ->withInput();
        }

        $this->patientIdentifierService->assignPid($patient);

        $appointment = Appointment::where('patient_id', $patient->id)
            ->where('status', 'scheduled')
            ->orderByDesc('appointment_date')
            ->first();

        if ($appointment) {
            $appointment->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
            ]);
        }

        return redirect()->route('appointments.check-in')
            ->with('success', 'Patient checked in successfully! Permanent PID assigned: ' . $patient->fresh()->pid);
    }
}
