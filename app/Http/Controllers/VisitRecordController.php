<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\VisitBillingService;
use App\Models\VisitChiefComplaint;
use App\Models\VisitDiagnosis;
use App\Models\VisitHistory;
use App\Models\VisitLabResult;
use App\Models\VisitPrescription;
use App\Models\VisitRadiologyResult;
use App\Models\EncounterVitalsDetail;
use App\Models\BillingMaster;
use App\Models\HospitalServiceCategory;
use App\Services\LabRequestService;
use App\Services\MasterServiceCreationService;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VisitRecordController extends Controller
{
    public function __construct(
        protected LabRequestService $labRequestService,
        protected MasterServiceCreationService $masterServiceCreationService
    ) {}
    public function storeVital(Request $request, Visit $visit)
    {
        $validator = Validator::make($request->all(), [
            'blood_pressure' => 'nullable|string|max:20',
            'pulse' => 'nullable|integer|min:0|max:300',
            'temperature' => 'nullable|numeric|min:90|max:110',
            'respiratory_rate' => 'nullable|integer|min:0|max:100',
            'oxygen_saturation' => 'nullable|numeric|min:0|max:100',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'notes' => 'nullable|string',
            'recorded_at' => 'nullable|date',
        ]);

        $validator->after(function ($validator) use ($request) {
            $fields = [
                'blood_pressure',
                'pulse',
                'temperature',
                'respiratory_rate',
                'oxygen_saturation',
                'weight',
                'height',
                'notes',
            ];

            $hasValue = collect($fields)->contains(fn ($field) => filled($request->input($field)));

            if (!$hasValue) {
                $validator->errors()->add('vitals', 'Enter at least one vital measurement before saving.');
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->route('visits.show', ['visit' => $visit->id, 'tab' => 'vitals'])
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $recordedAt = $validated['recorded_at'] ?? now();
        unset($validated['recorded_at']);

        $vitalsDetails = collect($validated)
            ->only([
                'blood_pressure',
                'pulse',
                'temperature',
                'respiratory_rate',
                'oxygen_saturation',
                'weight',
                'height',
                'notes',
            ])
            ->filter(fn ($value) => filled($value))
            ->all();

        EncounterVitalsDetail::create([
            'visit_id' => $visit->id,
            'vitals_details' => $vitalsDetails,
            'recorded_at' => $recordedAt,
            'saved_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('visits.show', ['visit' => $visit->id, 'tab' => 'vitals'])
            ->with('success', 'Vitals recorded successfully.');
    }

    public function storeDiagnosis(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'diagnosis_code' => 'nullable|string|max:50',
            'diagnosis_name' => 'required|string|max:255',
            'severity' => 'nullable|in:mild,moderate,severe',
            'notes' => 'nullable|string',
        ]);

        $validated['visit_id'] = $visit->id;
        VisitDiagnosis::create($validated);

        return redirect()->route('visits.show', [$visit->id, 'tab' => 'diagnosis'])
            ->with('success', 'Diagnosis added successfully.');
    }

    public function storeChiefComplaint(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'complaint' => 'required|string',
            'duration' => 'nullable|string|max:100',
            'severity' => 'nullable|in:mild,moderate,severe',
            'notes' => 'nullable|string',
        ]);

        $validated['visit_id'] = $visit->id;
        VisitChiefComplaint::create($validated);

        return redirect()->route('visits.show', [$visit->id, 'tab' => 'chief-complaints'])
            ->with('success', 'Chief complaint added successfully.');
    }

    public function storeHistory(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'history_type' => 'required|in:medical,surgical,family,social,allergy,medication',
            'description' => 'required|string',
        ]);

        $validated['visit_id'] = $visit->id;
        VisitHistory::create($validated);

        return redirect()->route('visits.show', [$visit->id, 'tab' => 'history'])
            ->with('success', 'History entry added successfully.');
    }

    public function storeBillingService(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'service_type' => ['required', Rule::in(['all', 'hospital', 'laboratory'])],
            'billing_item_code' => 'required_if:service_type,all|nullable|string|exists:billing_master,item_code',
            'category_nr' => 'required_if:service_type,hospital|nullable|integer|exists:hospital_service_category,nr',
            'department' => [
                'required_if:service_type,laboratory',
                'nullable',
                Rule::in(array_keys(config('diagnosis.lab_departments', []))),
            ],
            'service_name' => 'required_unless:service_type,all|nullable|string|max:250',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $serviceName = '';
        $serviceCode = null;
        $isLab = false;
        $labDepartment = null;
        $sampleType = 'Serum';
        $labTestId = null;

        if ($validated['service_type'] === 'all') {
            $catalogItem = BillingMaster::query()->findOrFail($validated['billing_item_code']);
            $serviceName = $catalogItem->item_description
                ?: $catalogItem->item_long_description
                ?: $catalogItem->item_code;
            $serviceCode = $catalogItem->item_code;

            if ((float) $validated['unit_price'] <= 0 && $catalogItem->item_unit_cost) {
                $validated['unit_price'] = $catalogItem->item_unit_cost;
            }

            $labContext = $this->masterServiceCreationService->resolveLabFromBillingItem($catalogItem);
            if ($labContext) {
                $isLab = true;
                $labDepartment = $labContext['department'];
                $sampleType = $labContext['sample_type'];
                $labTestId = $labContext['lab_test_id'];
            }
        } elseif ($validated['service_type'] === 'hospital') {
            $category = HospitalServiceCategory::query()->findOrFail($validated['category_nr']);
            $created = $this->masterServiceCreationService->createHospitalService(
                $category,
                $validated['service_name'],
                (float) $validated['unit_price']
            );

            $serviceName = $validated['service_name'];
            $serviceCode = $created['item_code'];
        } else {
            $created = $this->masterServiceCreationService->createLaboratoryService(
                $validated['department'],
                $validated['service_name'],
                (float) $validated['unit_price']
            );

            $serviceName = $validated['service_name'];
            $serviceCode = $created['item_code'];
            $isLab = true;
            $labDepartment = $created['department'];
            $labTestId = $created['lab_test_id'] ?? null;
        }

        $totalAmount = $validated['quantity'] * $validated['unit_price'];

        $billingService = VisitBillingService::create([
            'visit_id' => $visit->id,
            'service_name' => $serviceName,
            'service_code' => $serviceCode,
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_amount' => $totalAmount,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($isLab && $labDepartment) {
            $this->labRequestService->createFromBilling(
                $visit,
                $billingService,
                $labDepartment,
                $sampleType,
                $labTestId
            );
        }

        $message = $isLab
            ? 'Lab test billed successfully. Request sent to Sample Acknowledgement.'
            : 'Billing service added successfully.';

        if ($request->input('redirect_to') === 'billing') {
            return redirect()
                ->route('billing.show', ['visit' => $visit, 'from' => $request->input('from')])
                ->with('success', $message);
        }

        return redirect()->route('visits.show', [$visit->id, 'tab' => 'billing'])
            ->with('success', $message);
    }

    public function storeLabResult(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'test_name' => 'required|string|max:255',
            'test_code' => 'nullable|string|max:50',
            'result_value' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'reference_range' => 'nullable|string|max:100',
            'status' => 'required|in:normal,abnormal,critical',
            'notes' => 'nullable|string',
            'tested_at' => 'nullable|date',
        ]);

        $validated['visit_id'] = $visit->id;
        $validated['tested_at'] = $validated['tested_at'] ?? now();

        VisitLabResult::create($validated);

        return redirect()->route('visits.show', [$visit->id, 'tab' => 'lab-results'])
            ->with('success', 'Lab result added successfully.');
    }

    public function storeRadiologyResult(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'study_type' => 'required|string|max:255',
            'body_part' => 'nullable|string|max:100',
            'findings' => 'nullable|string',
            'impression' => 'nullable|string',
            'status' => 'required|in:pending,completed,reviewed',
            'reported_at' => 'nullable|date',
        ]);

        $validated['visit_id'] = $visit->id;
        $validated['reported_at'] = $validated['reported_at'] ?? now();

        VisitRadiologyResult::create($validated);

        return redirect()->route('visits.show', [$visit->id, 'tab' => 'radiology'])
            ->with('success', 'Radiology result added successfully.');
    }

    public function storePrescription(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'prescription_image' => 'required|string',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $imageData = $validated['prescription_image'];

        if (! preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
            return redirect()->route('visits.show', [$visit->id, 'tab' => 'prescription'])
                ->withErrors(['prescription_image' => 'Invalid prescription image. Please write on the pad and try again.']);
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $decoded = base64_decode(substr($imageData, strpos($imageData, ',') + 1));

        if ($decoded === false || strlen($decoded) < 100) {
            return redirect()->route('visits.show', [$visit->id, 'tab' => 'prescription'])
                ->withErrors(['prescription_image' => 'Prescription pad is empty. Please write before saving.']);
        }

        $filename = 'prescriptions/visit-' . $visit->id . '/' . uniqid('rx_') . '.' . $extension;
        Storage::disk('public')->put($filename, $decoded);

        VisitPrescription::create([
            'visit_id' => $visit->id,
            'doctor_id' => auth()->id(),
            'title' => $validated['title'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'image_path' => $filename,
        ]);

        return redirect()->route('visits.show', [$visit->id, 'tab' => 'prescription'])
            ->with('success', 'Doctor prescription saved successfully.');
    }
}
