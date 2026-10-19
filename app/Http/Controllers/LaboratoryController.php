<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\VisitLabResult;
use App\Services\LabSampleNumberService;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    public function __construct(
        protected LabSampleNumberService $sampleNumberService
    ) {}

    public function index()
    {
        $stageCounts = collect(config('diagnosis.lab_workflow_stages'))
            ->mapWithKeys(fn ($stage, $key) => [
                $key => LabRequest::where('status', $key)->count(),
            ]);

        $recentRequests = LabRequest::with(['visit.patient', 'visit.doctor'])
            ->latest()
            ->limit(10)
            ->get();

        return view('laboratory.index', compact('stageCounts', 'recentRequests'));
    }

    public function acknowledgementIndex(Request $request)
    {
        $requests = $this->filteredRequests(LabRequest::STATUS_ACKNOWLEDGEMENT, $request);

        return view('laboratory.acknowledgement.index', compact('requests'));
    }

    public function acknowledgementShow(LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_ACKNOWLEDGEMENT);

        $labRequest->load(['visit.patient', 'visit.doctor', 'billingService', 'labTest']);

        return view('laboratory.acknowledgement.show', compact('labRequest'));
    }

    public function acknowledge(Request $request, LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_ACKNOWLEDGEMENT);

        $validated = $request->validate([
            'acknowledgement_notes' => 'nullable|string|max:1000',
        ]);

        $sampleNumber = $this->sampleNumberService->generate(
            $labRequest->lab_department,
            $labRequest->sample_type
        );

        $labRequest->update([
            'sample_number' => $sampleNumber,
            'acknowledged_at' => now(),
            'acknowledged_by' => auth()->id(),
            'acknowledgement_notes' => $validated['acknowledgement_notes'] ?? null,
            'status' => LabRequest::STATUS_RECEIVE,
        ]);

        return redirect()
            ->route('laboratory.receive.show', $labRequest)
            ->with('success', "Sample acknowledged. Sample number {$sampleNumber} generated. Proceed to Sample Receive.");
    }

    public function receiveIndex(Request $request)
    {
        $requests = $this->filteredRequests(LabRequest::STATUS_RECEIVE, $request);

        return view('laboratory.receive.index', compact('requests'));
    }

    public function receiveShow(LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_RECEIVE);

        $labRequest->load(['visit.patient', 'visit.doctor', 'acknowledgedByUser']);

        return view('laboratory.receive.show', compact('labRequest'));
    }

    public function receive(Request $request, LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_RECEIVE);

        $validated = $request->validate([
            'receive_notes' => 'nullable|string|max:1000',
        ]);

        $labRequest->update([
            'received_at' => now(),
            'received_by' => auth()->id(),
            'receive_notes' => $validated['receive_notes'] ?? null,
            'status' => LabRequest::STATUS_REPORT_ENTRY,
        ]);

        return redirect()
            ->route('laboratory.report-entry.show', $labRequest)
            ->with('success', 'Sample received. Proceed to Report Entry.');
    }

    public function reportEntryIndex(Request $request)
    {
        $requests = $this->filteredRequests(LabRequest::STATUS_REPORT_ENTRY, $request);

        return view('laboratory.report-entry.index', compact('requests'));
    }

    public function reportEntryShow(LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_REPORT_ENTRY);

        $labRequest->load(['visit.patient', 'visit.doctor', 'receivedByUser', 'labTest']);

        return view('laboratory.report-entry.show', compact('labRequest'));
    }

    public function saveReportEntry(Request $request, LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_REPORT_ENTRY);

        $validated = $request->validate([
            'result_value' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'reference_range' => 'nullable|string|max:100',
            'result_status' => 'required|in:normal,abnormal,critical',
            'report_notes' => 'nullable|string|max:1000',
        ]);

        $labRequest->update(array_merge($validated, [
            'reported_at' => now(),
            'reported_by' => auth()->id(),
            'is_rejected' => false,
            'rejection_reason' => null,
            'status' => LabRequest::STATUS_AUTHORIZATION,
        ]));

        return redirect()
            ->route('laboratory.authorization.show', $labRequest)
            ->with('success', 'Report entry saved. Sent for Authorization.');
    }

    public function authorizationIndex(Request $request)
    {
        $requests = $this->filteredRequests(LabRequest::STATUS_AUTHORIZATION, $request);

        return view('laboratory.authorization.index', compact('requests'));
    }

    public function authorizationShow(LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_AUTHORIZATION);

        $labRequest->load(['visit.patient', 'visit.doctor', 'reportedByUser']);

        return view('laboratory.authorization.show', compact('labRequest'));
    }

    public function authorize(Request $request, LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_AUTHORIZATION);

        $validated = $request->validate([
            'authorization_notes' => 'nullable|string|max:1000',
        ]);

        $labRequest->update([
            'authorized_at' => now(),
            'authorized_by' => auth()->id(),
            'authorization_notes' => $validated['authorization_notes'] ?? null,
            'is_rejected' => false,
            'status' => LabRequest::STATUS_REPORT_DATA,
        ]);

        VisitLabResult::create([
            'visit_id' => $labRequest->visit_id,
            'test_name' => $labRequest->test_name,
            'test_code' => $labRequest->test_code ?? $labRequest->sample_number,
            'result_value' => $labRequest->result_value,
            'unit' => $labRequest->unit,
            'reference_range' => $labRequest->reference_range,
            'status' => $labRequest->result_status,
            'notes' => $labRequest->report_notes,
            'tested_at' => $labRequest->reported_at ?? now(),
        ]);

        return redirect()
            ->route('laboratory.report-data.show', $labRequest)
            ->with('success', 'Result authorized and published to Report Data.');
    }

    public function reject(Request $request, LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_AUTHORIZATION);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $labRequest->update([
            'is_rejected' => true,
            'rejection_reason' => $validated['rejection_reason'],
            'authorized_at' => null,
            'authorized_by' => null,
            'authorization_notes' => null,
            'status' => LabRequest::STATUS_REPORT_ENTRY,
        ]);

        return redirect()
            ->route('laboratory.report-entry.show', $labRequest)
            ->with('success', 'Result rejected. Sent back to Report Entry for correction.');
    }

    public function reportDataIndex(Request $request)
    {
        $query = LabRequest::with([
                'visit.patient',
                'visit.doctor',
                'reportedByUser',
                'authorizedByUser',
            ])
            ->where('status', LabRequest::STATUS_REPORT_DATA)
            ->orderByDesc('authorized_at');

        if ($request->filled('department')) {
            $query->where('lab_department', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhere('sample_number', 'like', "%{$search}%")
                    ->orWhere('test_name', 'like', "%{$search}%")
                    ->orWhereHas('visit.patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('pid', 'like', "%{$search}%");
                    });
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('laboratory.report-data.index', compact('requests'));
    }

    public function reportDataPrint(LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_REPORT_DATA);

        $labRequest->load(['visit.patient', 'visit.doctor', 'authorizedByUser', 'reportedByUser']);

        return view('laboratory.report-data.print', compact('labRequest'));
    }

    public function reportDataShow(LabRequest $labRequest)
    {
        $this->ensureStatus($labRequest, LabRequest::STATUS_REPORT_DATA);

        $labRequest->load(['visit.patient', 'visit.doctor', 'authorizedByUser', 'reportedByUser']);

        return view('laboratory.report-data.show', compact('labRequest'));
    }

    protected function filteredRequests(string $status, Request $request)
    {
        $query = LabRequest::with(['visit.patient', 'visit.doctor'])
            ->where('status', $status)
            ->orderByDesc('created_at');

        if ($request->filled('department')) {
            $query->where('lab_department', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhere('sample_number', 'like', "%{$search}%")
                    ->orWhere('test_name', 'like', "%{$search}%")
                    ->orWhereHas('visit.patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('pid', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(20)->withQueryString();
    }

    protected function ensureStatus(LabRequest $labRequest, string $expectedStatus): void
    {
        if ($labRequest->status !== $expectedStatus) {
            abort(404, 'This lab request is not in the expected workflow stage.');
        }
    }
}
