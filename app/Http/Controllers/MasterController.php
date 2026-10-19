<?php

namespace App\Http\Controllers;

use App\Models\HospitalService;
use App\Models\HospitalServiceCategory;
use App\Models\LegacyLabTest;
use App\Models\User;
use App\Services\HospitalSettingService;
use App\Services\MasterServiceCreationService;
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
        protected MasterServiceCreationService $masterServiceCreationService
    ) {}

    public function index()
    {
        return view('master.index', [
            'showPhpMyAdmin' => $this->phpMyAdminAvailable(),
        ]);
    }

    public function lisMapping()
    {
        $departments = config('diagnosis.lab_departments', []);
        $legacyTests = LegacyLabTest::query()
            ->whereIn('group_id', array_keys($departments))
            ->orderBy('group_id')
            ->orderBy('name')
            ->limit(200)
            ->get();

        return view('master.lis-mapping.index', compact('departments', 'legacyTests'));
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
        $recentHospitalServices = HospitalService::query()
            ->orderByDesc('code')
            ->limit(10)
            ->get();
        $recentLabServices = LegacyLabTest::query()
            ->whereIn('group_id', array_keys($labDepartments))
            ->orderByDesc('create_time')
            ->limit(10)
            ->get();

        return view('master.services.create', compact(
            'hospitalCategories',
            'labDepartments',
            'recentHospitalServices',
            'recentLabServices'
        ));
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

        $result = $this->masterServiceCreationService->createLaboratoryService(
            $validated['department'],
            $validated['service_name'],
            (float) $validated['unit_price']
        );

        return redirect()
            ->route('master.services.create')
            ->with('success', "Laboratory service saved. Item code: {$result['item_code']}");
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
