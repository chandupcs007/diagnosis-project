<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\VisitController;
use App\Http\Controllers\VisitRecordController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\BillingController;

// Public routes
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->route('dashboard')->with('enter_fullscreen', true);
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

// Registration Routes
Route::get('/register', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.register');
})->name('register');

Route::post('/register', function () {
    $credentials = request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Create user
    $user = \App\Models\User::create([
        'name' => request('name'),
        'email' => request('email'),
        'password' => bcrypt(request('password')),
    ]);

    // Log in the user
    Auth::login($user);

    return redirect()->route('dashboard')
        ->with('success', 'Account created successfully! Welcome to ' . config('diagnosis.name'))
        ->with('enter_fullscreen', true);
});

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Appointment Routes
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/check-in', [AppointmentController::class, 'checkInForm'])->name('appointments.check-in');
    Route::post('/appointments/check-in', [AppointmentController::class, 'checkIn'])->name('appointments.check-in.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments-api/doctors', [AppointmentController::class, 'doctors'])->name('appointments.doctors');
    Route::get('/appointments-api/slots', [AppointmentController::class, 'slots'])->name('appointments.slots');
    Route::post('/appointments-api/lookup-patient', [AppointmentController::class, 'lookupPatient'])->name('appointments.lookup-patient');

    // Patient Routes
    Route::post('/patients-api/check-duplicate', [PatientController::class, 'checkDuplicate'])->name('patients.check-duplicate');
    Route::post('/patients-api/search-fingerprint', [PatientController::class, 'searchByFingerprint'])->name('patients.search-fingerprint');
    Route::get('/patients/{patient}/id-card', [PatientController::class, 'idCard'])->name('patients.id-card');
    Route::resource('patients', PatientController::class);

    // Billing Routes
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/patients/{section}', [BillingController::class, 'patients'])
        ->where('section', 'current|previous')
        ->name('billing.patients');
    Route::get('/billing/visits/{visit}', [BillingController::class, 'show'])->name('billing.show');
    Route::put('/billing/visits/{visit}/payer-type', [BillingController::class, 'updatePayerType'])->name('billing.payer-type.update');
    Route::put('/billing/visits/{visit}/status', [BillingController::class, 'updateStatus'])->name('billing.status.update');
    Route::post('/billing/visits/{visit}/payments', [BillingController::class, 'storePayment'])->name('billing.payments.store');
    Route::get('/billing/bills/{bill}/print', [BillingController::class, 'printBill'])->name('billing.bills.print');

    // Laboratory Routes
    Route::get('/laboratory', [LaboratoryController::class, 'index'])->name('laboratory.index');
    Route::get('/laboratory/acknowledgement', [LaboratoryController::class, 'acknowledgementIndex'])->name('laboratory.acknowledgement.index');
    Route::get('/laboratory/acknowledgement/{labRequest}', [LaboratoryController::class, 'acknowledgementShow'])->name('laboratory.acknowledgement.show');
    Route::post('/laboratory/acknowledgement/{labRequest}', [LaboratoryController::class, 'acknowledge'])->name('laboratory.acknowledgement.store');
    Route::get('/laboratory/receive', [LaboratoryController::class, 'receiveIndex'])->name('laboratory.receive.index');
    Route::get('/laboratory/receive/{labRequest}', [LaboratoryController::class, 'receiveShow'])->name('laboratory.receive.show');
    Route::post('/laboratory/receive/{labRequest}', [LaboratoryController::class, 'receive'])->name('laboratory.receive.store');
    Route::get('/laboratory/report-entry', [LaboratoryController::class, 'reportEntryIndex'])->name('laboratory.report-entry.index');
    Route::get('/laboratory/report-entry/{labRequest}', [LaboratoryController::class, 'reportEntryShow'])->name('laboratory.report-entry.show');
    Route::post('/laboratory/report-entry/{labRequest}', [LaboratoryController::class, 'saveReportEntry'])->name('laboratory.report-entry.store');
    Route::get('/laboratory/authorization', [LaboratoryController::class, 'authorizationIndex'])->name('laboratory.authorization.index');
    Route::get('/laboratory/authorization/{labRequest}', [LaboratoryController::class, 'authorizationShow'])->name('laboratory.authorization.show');
    Route::post('/laboratory/authorization/{labRequest}/approve', [LaboratoryController::class, 'authorize'])->name('laboratory.authorization.approve');
    Route::post('/laboratory/authorization/{labRequest}/reject', [LaboratoryController::class, 'reject'])->name('laboratory.authorization.reject');
    Route::get('/laboratory/report-data', [LaboratoryController::class, 'reportDataIndex'])->name('laboratory.report-data.index');
    Route::get('/laboratory/report-data/{labRequest}', [LaboratoryController::class, 'reportDataShow'])->name('laboratory.report-data.show');
    Route::get('/laboratory/report-data/{labRequest}/print', [LaboratoryController::class, 'reportDataPrint'])->name('laboratory.report-data.print');

    // Add these visit routes
    Route::get('/visits/create/{patient}', [VisitController::class, 'create'])->name('visits.create');
    Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
    Route::get('/visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
    Route::get('/patients/{patient}/visits', [VisitController::class, 'patientVisits'])->name('visits.patient-index');

    Route::post('/visits/{visit}/vitals', [VisitRecordController::class, 'storeVital'])->name('visits.vitals.store');
    Route::post('/visits/{visit}/diagnoses', [VisitRecordController::class, 'storeDiagnosis'])->name('visits.diagnoses.store');
    Route::post('/visits/{visit}/chief-complaints', [VisitRecordController::class, 'storeChiefComplaint'])->name('visits.chief-complaints.store');
    Route::post('/visits/{visit}/histories', [VisitRecordController::class, 'storeHistory'])->name('visits.histories.store');
    Route::post('/visits/{visit}/billing-services', [VisitRecordController::class, 'storeBillingService'])->name('visits.billing-services.store');
    Route::post('/visits/{visit}/lab-results', [VisitRecordController::class, 'storeLabResult'])->name('visits.lab-results.store');
    Route::post('/visits/{visit}/radiology-results', [VisitRecordController::class, 'storeRadiologyResult'])->name('visits.radiology-results.store');
    Route::post('/visits/{visit}/prescriptions', [VisitRecordController::class, 'storePrescription'])->name('visits.prescriptions.store');

    // Diagnosis Routes
    Route::get('/diagnosis', [DiagnosisController::class, 'index'])->name('diagnosis.index');
    Route::get('/diagnosis/create', [DiagnosisController::class, 'create'])->name('diagnosis.create');
    Route::post('/diagnosis', [DiagnosisController::class, 'store'])->name('diagnosis.store');

    // Report Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Master Routes
    Route::get('/master', [MasterController::class, 'index'])->name('master.index');
    Route::get('/master/users/create', [MasterController::class, 'createUser'])->name('master.users.create');
    Route::post('/master/users', [MasterController::class, 'storeUser'])->name('master.users.store');
    Route::get('/master/services/create', [MasterController::class, 'createService'])->name('master.services.create');
    Route::get('/master/services/next-hospital-code', [MasterController::class, 'nextHospitalServiceCode'])->name('master.services.next-hospital-code');
    Route::get('/master/services/next-laboratory-code', [MasterController::class, 'nextLaboratoryServiceCode'])->name('master.services.next-laboratory-code');
    Route::post('/master/services', [MasterController::class, 'storeService'])->name('master.services.store');
    Route::get('/master/services/hospital/search', [MasterController::class, 'searchHospitalServices'])->name('master.services.hospital.search');
    Route::get('/master/services/hospital/{code}/logs', [MasterController::class, 'hospitalServiceLogs'])->name('master.services.hospital.logs');
    Route::get('/master/services/hospital/{code}', [MasterController::class, 'showHospitalService'])->name('master.services.hospital.show');
    Route::put('/master/services/hospital/{code}', [MasterController::class, 'updateHospitalService'])->name('master.services.hospital.update');
    Route::get('/master/services/laboratory/search', [MasterController::class, 'searchLaboratoryServices'])->name('master.services.laboratory.search');
    Route::get('/master/services/laboratory/{code}/logs', [MasterController::class, 'laboratoryServiceLogs'])->name('master.services.laboratory.logs');
    Route::get('/master/services/laboratory/{code}', [MasterController::class, 'showLaboratoryService'])->name('master.services.laboratory.show');
    Route::put('/master/services/laboratory/{code}', [MasterController::class, 'updateLaboratoryService'])->name('master.services.laboratory.update');
    Route::get('/master/hospital', [MasterController::class, 'hospitalDetails'])->name('master.hospital.edit');
    Route::put('/master/hospital', [MasterController::class, 'updateHospital'])->name('master.hospital.update');
    Route::get('/master/doctors/create', [MasterController::class, 'createDoctor'])->name('master.doctors.create');
    Route::post('/master/doctors', [MasterController::class, 'storeDoctor'])->name('master.doctors.store');
    Route::get('/master/lis-mapping', [MasterController::class, 'lisMapping'])->name('master.lis-mapping.index');
    Route::get('/master/lis-mapping/analyzers', [MasterController::class, 'lisAnalyzerMapping'])->name('master.lis-mapping.analyzers');
    Route::get('/master/lis-mapping/analyzers/{analyzerId}/his-mappings', [MasterController::class, 'analyzerHisMappings'])->name('master.lis-mapping.analyzers.his-mappings');
    Route::post('/master/lis-mapping/analyzers', [MasterController::class, 'storeLisAnalyzer'])->name('master.lis-mapping.analyzers.store');
    Route::get('/master/lis-mapping/analyzer-assay-codes', [MasterController::class, 'lisAnalyzerAssayMapping'])->name('master.lis-mapping.analyzer-assay-codes');
    Route::post('/master/lis-mapping/analyzer-assay-codes', [MasterController::class, 'storeLisAnalyzerAssayCode'])->name('master.lis-mapping.analyzer-assay-codes.store');
    Route::get('/master/lis-mapping/assay-his-codes/analyzer-assays', [MasterController::class, 'lisAssayCodesForAnalyzer'])->name('master.lis-mapping.assay-his-codes.analyzer-assays');
    Route::get('/master/lis-mapping/assay-his-codes', [MasterController::class, 'lisAssayHisMapping'])->name('master.lis-mapping.assay-his-codes');
    Route::post('/master/lis-mapping/assay-his-codes', [MasterController::class, 'storeLisAssayHisMapping'])->name('master.lis-mapping.assay-his-codes.store');
    Route::get('/master/phpmyadmin', [MasterController::class, 'phpMyAdmin'])->name('master.phpmyadmin');



    // Doctor registration route (if you want separate registration)
Route::get('/register/doctor', function () {
    $departments = config('diagnosis.departments');
    return view('auth.register-doctor', compact('departments'));
})->name('register.doctor');

Route::post('/register/doctor', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'specialization' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'qualification' => 'required|string|max:255',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'doctor',
        'specialization' => $validated['specialization'],
        'phone' => $validated['phone'],
        'qualification' => $validated['qualification'],
    ]);

    Auth::login($user);

    return redirect()->route('dashboard')
        ->with('success', 'Doctor account created successfully!')
        ->with('enter_fullscreen', true);
});


    // Logout route
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});