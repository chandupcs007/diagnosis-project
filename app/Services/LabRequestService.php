<?php

namespace App\Services;

use App\Models\LabRequest;
use App\Models\LabTest;
use App\Models\Visit;
use App\Models\VisitBillingService;

class LabRequestService
{
    public function createFromBilling(
        Visit $visit,
        VisitBillingService $billingService,
        string $labDepartment,
        string $sampleType,
        ?int $labTestId = null
    ): LabRequest {
        $labTest = $labTestId ? LabTest::find($labTestId) : null;

        return LabRequest::create([
            'request_number' => LabRequest::generateRequestNumber(),
            'visit_id' => $visit->id,
            'visit_billing_service_id' => $billingService->id,
            'lab_test_id' => $labTest?->id,
            'test_name' => $billingService->service_name,
            'test_code' => $billingService->service_code,
            'lab_department' => $labDepartment,
            'sample_type' => $sampleType,
            'status' => LabRequest::STATUS_ACKNOWLEDGEMENT,
        ]);
    }
}
