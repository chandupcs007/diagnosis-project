<?php

namespace App\Http\Controllers;

use App\Models\AnalyzerAssaycodeDetail;
use App\Models\AnalyzerAssaycodeToHis;
use App\Models\AnalyzerDetail;
use App\Models\HospitalService;
use App\Models\HospitalServiceCategory;
use App\Models\LabTestParam;
use App\Models\LegacyLabTest;
use App\Models\User;
use App\Services\HospitalSettingService;
use App\Services\MasterServiceCreationService;
use App\Services\MasterServiceMaintenanceService;
use App\Services\UserPhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MasterController extends Controller
{
    public function __construct(
        protected HospitalSettingService $hospitalSettingService,
        protected UserPhotoService $userPhotoService,
        protected MasterServiceCreationService $masterServiceCreationService,
        protected MasterServiceMaintenanceService $masterServiceMaintenanceService
    ) {}

    public function index()
    {
        return view('master.index', [
            'showPhpMyAdmin' => $this->phpMyAdminAvailable(),
        ]);
    }

    public function lisMapping()
    {
        return view('master.lis-mapping.index');
    }

    public function lisAnalyzerMapping()
    {
        $analyzers = AnalyzerDetail::query()
            ->orderBy('analyzer_name')
            ->get();

        return view('master.lis-mapping.analyzers', compact('analyzers'));
    }

    public function analyzerHisMappings(string $analyzerId)
    {
        abort_unless(
            AnalyzerDetail::query()->where('analyzer_id', $analyzerId)->exists(),
            404
        );

        $analyzer = AnalyzerDetail::query()->where('analyzer_id', $analyzerId)->first();
        $mappings = AnalyzerAssaycodeToHis::query()
            ->where('analyzer_id', $analyzerId)
            ->with(['analyzerAssaycode', 'labTest'])
            ->orderBy('analyzer_assaycode_id')
            ->orderBy('his_test_code')
            ->get();

        return response()->json([
            'analyzer' => [
                'analyzer_id' => $analyzer->analyzer_id,
                'analyzer_name' => $analyzer->analyzer_name,
            ],
            'mappings' => $mappings->map(function ($mapping) {
                return [
                    'assay_code' => $mapping->analyzerAssaycode?->assay_code,
                    'analyzer_assaycode_id' => $mapping->analyzer_assaycode_id,
                    'his_test_code' => $mapping->his_test_code,
                    'test_name' => $mapping->labTest?->name,
                    'created_at' => $mapping->created_at?->format('Y-m-d H:i'),
                ];
            })->values(),
        ]);
    }

    public function storeLisAnalyzer(Request $request)
    {
        $validated = $request->validate([
            'analyzer_name' => 'required|string|max:150',
            'analyzer_id' => 'required|string|max:50|unique:analyzer_details,analyzer_id',
        ]);

        AnalyzerDetail::create($validated);

        return redirect()
            ->route('master.lis-mapping.analyzers')
            ->with('success', 'Analyzer saved successfully.');
    }

    public function lisAnalyzerAssayMapping()
    {
        $analyzers = AnalyzerDetail::query()
            ->withCount('assayCodes')
            ->orderBy('analyzer_name')
            ->get();

        $assayMappings = AnalyzerAssaycodeDetail::query()
            ->with('analyzer')
            ->orderBy('analyzer_id')
            ->orderBy('assay_code')
            ->get();

        return view('master.lis-mapping.analyzer-assay-codes', compact('analyzers', 'assayMappings'));
    }

    public function storeLisAnalyzerAssayCode(Request $request)
    {
        $validated = $request->validate([
            'analyzer_id' => 'required|string|exists:analyzer_details,analyzer_id',
            'assay_code' => 'required|string|max:100',
        ]);

        $exists = AnalyzerAssaycodeDetail::query()
            ->where('analyzer_id', $validated['analyzer_id'])
            ->where('assay_code', $validated['assay_code'])
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['assay_code' => 'This assay code is already mapped for the selected analyzer.']);
        }

        AnalyzerAssaycodeDetail::create($validated);

        return redirect()
            ->route('master.lis-mapping.analyzer-assay-codes')
            ->with('success', 'Analyzer assay code saved successfully.');
    }

    public function lisAssayHisMapping()
    {
        $departments = config('diagnosis.lab_departments', []);
        $analyzers = AnalyzerDetail::query()
            ->whereHas('assayCodes')
            ->withCount('assayCodes')
            ->orderBy('analyzer_name')
            ->get();

        $registeredAnalyzers = AnalyzerDetail::query()
            ->orderBy('analyzer_name')
            ->get();

        $labTests = LegacyLabTest::query()
            ->whereIn('group_id', array_keys($departments))
            ->orderBy('name')
            ->get(['id', 'name', 'group_id']);

        $hisMappings = AnalyzerAssaycodeToHis::query()
            ->with(['analyzer', 'analyzerAssaycode', 'labTest'])
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return view('master.lis-mapping.assay-his-codes', compact(
            'analyzers',
            'registeredAnalyzers',
            'labTests',
            'hisMappings',
            'departments'
        ));
    }

    public function lisAssayCodesForAnalyzer(Request $request)
    {
        $validated = $request->validate([
            'analyzer_id' => 'required|string|exists:analyzer_details,analyzer_id',
        ]);

        $assays = AnalyzerAssaycodeDetail::query()
            ->where('analyzer_id', $validated['analyzer_id'])
            ->orderBy('assay_code')
            ->get(['id', 'analyzer_id', 'assay_code']);

        return response()->json(['assays' => $assays]);
    }

    public function storeLisAssayHisMapping(Request $request)
    {
        $validated = $request->validate([
            'analyzer_id' => 'required|string|exists:analyzer_details,analyzer_id',
            'analyzer_assaycode_id' => 'required|integer|exists:analyzer_assaycode_details,id',
            'his_test_codes' => 'required|array|min:1',
            'his_test_codes.*' => 'string|exists:lab_test,id',
        ]);

        $assay = AnalyzerAssaycodeDetail::query()->findOrFail($validated['analyzer_assaycode_id']);

        if ($assay->analyzer_id !== $validated['analyzer_id']) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['analyzer_assaycode_id' => 'Selected assay code does not belong to this analyzer.']);
        }

        $created = 0;
        foreach (array_unique($validated['his_test_codes']) as $testCode) {
            $row = AnalyzerAssaycodeToHis::query()->firstOrCreate(
                [
                    'analyzer_assaycode_id' => $assay->id,
                    'his_test_code' => $testCode,
                ],
                [
                    'analyzer_id' => $validated['analyzer_id'],
                ]
            );

            if ($row->wasRecentlyCreated) {
                $created++;
            }
        }

        $message = $created > 0
            ? "{$created} HIS test mapping(s) saved."
            : 'Selected HIS tests were already mapped for this assay code.';

        return redirect()
            ->route('master.lis-mapping.assay-his-codes')
            ->with('success', $message);
    }

    public function phpMyAdmin()
    {
        abort_unless($this->phpMyAdminAvailable(), 403, 'phpMyAdmin access requires a signed-in user.');

        $url = config('diagnosis.phpmyadmin.url');

        abort_if(empty($url), 404, 'phpMyAdmin URL is not configured.');

        return redirect()->away($url);
    }

    protected function phpMyAdminAvailable(): bool
    {
        if (!config('diagnosis.phpmyadmin.enabled', false)) {
            return false;
        }

        if (empty(config('diagnosis.phpmyadmin.url'))) {
            return false;
        }

        return auth()->check();
    }

    public function createUser()
    {
        $departments = collect(config('diagnosis.lab_departments'))
            ->pluck('name')
            ->merge(config('diagnosis.departments', []))
            ->push('Administration', 'Reception', 'Laboratory')
            ->unique()
            ->sort()
            ->values();

        return view('master.users.create', compact('departments'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'staff', 'user'])],
            'department' => 'required|string|max:255',
            'photo_data' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department' => $validated['department'],
        ]);

        if ($request->filled('photo_data')) {
            try {
                $this->userPhotoService->savePhotoFromBase64($user, $request->input('photo_data'));
            } catch (\InvalidArgumentException $exception) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['photo_data' => 'Invalid user photo. Please capture the photo again.']);
            }
        }

        return redirect()
            ->route('master.users.create')
            ->with('success', 'User created successfully.');
    }

    public function createService()
    {
        $hospitalCategories = $this->masterServiceCreationService->hospitalCategories();
        $labDepartments = config('diagnosis.lab_departments', []);
        $selectableLabTests = $this->masterServiceCreationService->selectableLabTestsForProfiles();

        return view('master.services.create', compact(
            'hospitalCategories',
            'labDepartments',
            'selectableLabTests'
        ));
    }

    public function searchHospitalServices(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
        ]);

        $results = $this->masterServiceMaintenanceService->searchHospitalServices($validated['q'] ?? '');

        return response()->json(['results' => $results]);
    }

    public function showHospitalService(string $code)
    {
        return response()->json([
            'service' => $this->masterServiceMaintenanceService->hospitalServicePayload($code),
        ]);
    }

    public function updateHospitalService(Request $request, string $code)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:10',
            'description' => 'required|string|max:250',
            'unit_price' => 'required|numeric|min:0',
            'work_type' => 'nullable|string|max:20',
            'billing_description' => 'nullable|string|max:100',
            'billing_long_description' => 'nullable|string|max:100',
            'sub_level' => 'nullable|integer',
            'navision' => 'nullable|integer',
            'std_code' => 'nullable|string|max:1',
            'notes' => 'nullable|string',
            'remarks' => 'nullable|string',
            'inclusive' => 'nullable|string',
            'exclusive' => 'nullable|string',
        ]);

        $this->masterServiceMaintenanceService->updateHospitalService($code, $validated);

        return response()->json([
            'message' => 'Hospital service updated.',
            'service' => $this->masterServiceMaintenanceService->hospitalServicePayload($code),
        ]);
    }

    public function hospitalServiceLogs(string $code)
    {
        return response()->json([
            'logs' => $this->formatAuditLogs(
                $this->masterServiceMaintenanceService->auditLogs('hospital', $code)
            ),
        ]);
    }

    public function searchLaboratoryServices(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
        ]);

        $results = $this->masterServiceMaintenanceService->searchLaboratoryServices($validated['q'] ?? '');

        return response()->json(['results' => $results]);
    }

    public function showLaboratoryService(string $code)
    {
        return response()->json([
            'service' => $this->masterServiceMaintenanceService->laboratoryServicePayload($code),
        ]);
    }

    public function updateLaboratoryService(Request $request, string $code)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:250',
            'sample_type' => ['required', Rule::in(config('diagnosis.sample_types', []))],
            'unit_price' => 'required|numeric|min:0',
            'msr_unit' => 'nullable|string|max:80',
            'normal_range' => 'nullable|string',
            'is_visible' => 'nullable|in:0,1',
            'billing_description' => 'nullable|string|max:100',
            'billing_long_description' => 'nullable|string|max:100',
        ]);

        $this->masterServiceMaintenanceService->updateLaboratoryService($code, $validated);

        return response()->json([
            'message' => 'Laboratory service updated.',
            'service' => $this->masterServiceMaintenanceService->laboratoryServicePayload($code),
        ]);
    }

    public function laboratoryServiceLogs(string $code)
    {
        return response()->json([
            'logs' => $this->formatAuditLogs(
                $this->masterServiceMaintenanceService->auditLogs('laboratory', $code)
            ),
        ]);
    }

    protected function formatAuditLogs($logs): array
    {
        return $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'changed_at' => $log->created_at?->format('Y-m-d H:i:s') ?? '',
                'changed_by' => $log->user?->name ?? 'System',
                'before_data' => $log->before_data,
                'after_data' => $log->after_data,
            ];
        })->values()->all();
    }

    public function nextHospitalServiceCode(Request $request)
    {
        $validated = $request->validate([
            'category_nr' => 'required|integer|exists:hospital_service_category,nr',
        ]);

        $category = HospitalServiceCategory::findOrFail($validated['category_nr']);
        $itemCode = $this->masterServiceCreationService->nextHospitalItemCode($category->category);

        return response()->json([
            'item_code' => $itemCode,
            'category' => $category->category,
            'category_name' => $category->name,
        ]);
    }

    public function nextLaboratoryServiceCode(Request $request)
    {
        $validated = $request->validate([
            'department' => ['required', Rule::in(array_keys(config('diagnosis.lab_departments', [])))],
        ]);

        $itemCode = $this->masterServiceCreationService->nextLaboratoryItemCode($validated['department']);

        return response()->json([
            'item_code' => $itemCode,
            'department' => $validated['department'],
            'department_name' => config('diagnosis.lab_departments.' . $validated['department'] . '.name'),
        ]);
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_mode' => ['required', Rule::in(['hospital', 'laboratory'])],
            'service_name' => 'required|string|max:250',
            'unit_price' => 'required|numeric|min:0',
            'category_nr' => 'required_if:service_mode,hospital|nullable|integer|exists:hospital_service_category,nr',
            'department' => [
                'required_if:service_mode,laboratory',
                'nullable',
                Rule::in(array_keys(config('diagnosis.lab_departments', []))),
            ],
            'is_profile' => 'nullable|in:0,1',
            'sample_type' => [
                'required_if:service_mode,laboratory',
                'nullable',
                Rule::in(config('diagnosis.sample_types', [])),
            ],
            'profile_child_codes' => 'required_if:is_profile,1|array|min:1',
            'profile_child_codes.*' => 'string|exists:lab_test,id',
        ]);

        if ($validated['service_mode'] === 'hospital') {
            $category = HospitalServiceCategory::findOrFail($validated['category_nr']);

            $result = $this->masterServiceCreationService->createHospitalService(
                $category,
                $validated['service_name'],
                (float) $validated['unit_price']
            );

            return redirect()
                ->route('master.services.create')
                ->with('success', "Hospital service saved. Item code: {$result['item_code']}");
        }

        $isProfile = (int) ($validated['is_profile'] ?? 0) === 1;
        $childCodes = $isProfile ? array_values(array_unique($validated['profile_child_codes'] ?? [])) : [];

        if ($isProfile) {
            $validChildCount = LegacyLabTest::query()
                ->whereIn('id', $childCodes)
                ->count();

            $includesProfile = LabTestParam::query()
                ->where('is_profile', 1)
                ->whereIn('item_code', $childCodes)
                ->exists();

            if ($validChildCount !== count($childCodes) || $includesProfile) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['profile_child_codes' => 'Choose valid individual lab tests only (profiles cannot be nested).']);
            }
        }

        $result = $this->masterServiceCreationService->createLaboratoryService(
            $validated['department'],
            $validated['service_name'],
            (float) $validated['unit_price'],
            $isProfile,
            $childCodes,
            $validated['sample_type'] ?? 'Serum'
        );

        $message = $isProfile
            ? "Laboratory profile saved with " . count($childCodes) . " test(s). Item code: {$result['item_code']}"
            : "Laboratory service saved. Item code: {$result['item_code']}";

        return redirect()
            ->route('master.services.create')
            ->with('success', $message);
    }

    public function hospitalDetails()
    {
        $hospital = $this->hospitalSettingService->current();

        return view('master.hospital.edit', compact('hospital'));
    }

    public function updateHospital(Request $request)
    {
        $validated = $request->validate([
            'hospital_name' => 'required|string|max:255',
            'department_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:30',
            'contact_email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'return_message' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
        ]);

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $current = $this->hospitalSettingService->current();

            if ($current->logo_path) {
                Storage::disk('public')->delete($current->logo_path);
            }

            $extension = $request->file('logo')->getClientOriginalExtension();
            $logoPath = 'hospital/logo_' . Str::uuid() . '.' . $extension;
            $request->file('logo')->storeAs('', $logoPath, 'public');
        }

        $this->hospitalSettingService->update($validated, $logoPath);

        return redirect()
            ->route('master.hospital.edit')
            ->with('success', 'Hospital details updated successfully.');
    }

    public function createDoctor()
    {
        $departments = config('diagnosis.departments', []);

        return view('master.doctors.create', compact('departments'));
    }

    public function storeDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'specialization' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'qualification' => 'required|string|max:255',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'doctor',
            'specialization' => $validated['specialization'],
            'phone' => $validated['phone'],
            'qualification' => $validated['qualification'],
        ]);

        return redirect()
            ->route('master.doctors.create')
            ->with('success', 'Doctor created successfully.');
    }
}
