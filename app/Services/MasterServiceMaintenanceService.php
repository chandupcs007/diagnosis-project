<?php

namespace App\Services;

use App\Models\BillingMaster;
use App\Models\HospitalService;
use App\Models\LabTest;
use App\Models\LabTestParam;
use App\Models\LegacyLabTest;
use App\Models\MasterServiceAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterServiceMaintenanceService
{
    public function searchHospitalServices(string $query, int $limit = 25)
    {
        $query = trim($query);

        if ($query === '') {
            return collect();
        }

        return HospitalService::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('code', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%')
                        ->orWhere('category', 'like', '%' . $query . '%');
                });
            })
            ->orderBy('code')
            ->limit($limit)
            ->get(['code', 'category', 'description']);
    }

    public function searchLaboratoryServices(string $query, int $limit = 25)
    {
        $query = trim($query);
        $departments = array_keys(config('diagnosis.lab_departments', []));

        if ($query === '') {
            return collect();
        }

        return LegacyLabTest::query()
            ->whereIn('group_id', $departments)
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%')
                        ->orWhere('group_id', 'like', '%' . $query . '%');
                });
            })
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'group_id', 'name', 'sample']);
    }

    public function hospitalServicePayload(string $itemCode): array
    {
        $service = HospitalService::query()->findOrFail($itemCode);
        $billing = BillingMaster::query()->find($itemCode);

        return [
            'item_code' => $service->code,
            'category' => $service->category,
            'description' => $service->description,
            'unit_price' => $billing ? (float) $billing->item_unit_cost : 0,
            'inclusive' => $service->inclusive,
            'exclusive' => $service->exclusive,
            'notes' => $service->notes,
            'std_code' => $service->std_code,
            'sub_level' => $service->sub_level,
            'remarks' => $service->remarks,
            'navision' => $service->navision,
            'work_type' => $service->work_type,
            'billing_description' => $billing?->item_description,
            'billing_long_description' => $billing?->item_long_description,
        ];
    }

    public function laboratoryServicePayload(string $itemCode): array
    {
        $test = LegacyLabTest::query()->findOrFail($itemCode);
        $billing = BillingMaster::query()->find($itemCode);
        $param = LabTestParam::query()->find($itemCode);

        return [
            'item_code' => $test->id,
            'department' => $test->group_id,
            'department_label' => config('diagnosis.lab_departments.' . $test->group_id . '.name', $test->group_id),
            'name' => $test->name,
            'sample_type' => $test->sample,
            'msr_unit' => $test->msr_unit,
            'normal_range' => $test->normal_range,
            'is_visible' => (bool) $test->isVisible,
            'is_profile' => (int) ($param?->is_profile ?? 0),
            'unit_price' => $billing ? (float) $billing->item_unit_cost : 0,
            'billing_description' => $billing?->item_description,
            'billing_long_description' => $billing?->item_long_description,
        ];
    }

    public function updateHospitalService(string $itemCode, array $data): array
    {
        return DB::transaction(function () use ($itemCode, $data) {
            $service = HospitalService::query()->findOrFail($itemCode);
            $billing = BillingMaster::query()->findOrFail($itemCode);

            $before = $this->hospitalServicePayload($itemCode);

            $description = Str::limit(trim($data['description']), 250, '');
            $shortBilling = Str::limit(trim($data['billing_description'] ?? $description), 100, '');

            $service->update([
                'category' => $data['category'],
                'description' => $description,
                'inclusive' => $data['inclusive'] ?? $service->inclusive,
                'exclusive' => $data['exclusive'] ?? $service->exclusive,
                'notes' => $data['notes'] ?? '',
                'std_code' => $data['std_code'] ?? '',
                'sub_level' => (int) ($data['sub_level'] ?? $service->sub_level),
                'remarks' => $data['remarks'] ?? '',
                'navision' => (int) ($data['navision'] ?? $service->navision),
                'work_type' => $data['work_type'] ?? $service->work_type,
            ]);

            $billing->update(array_merge(
                $this->priceColumnsForUnitCost((float) $data['unit_price']),
                [
                    'item_description' => $shortBilling,
                    'item_long_description' => Str::limit(trim($data['billing_long_description'] ?? $description), 255, ''),
                    'modify_id' => Auth::user()?->name,
                    'modify_time' => now(),
                ]
            ));

            $after = $this->hospitalServicePayload($itemCode);
            $this->writeAuditLog('hospital', $itemCode, $before, $after);

            return $after;
        });
    }

    public function updateLaboratoryService(string $itemCode, array $data): array
    {
        return DB::transaction(function () use ($itemCode, $data) {
            $test = LegacyLabTest::query()->findOrFail($itemCode);
            $billing = BillingMaster::query()->findOrFail($itemCode);

            $before = $this->laboratoryServicePayload($itemCode);

            $name = Str::limit(trim($data['name']), 250, '');
            $shortBilling = Str::limit(trim($data['billing_description'] ?? $name), 100, '');

            $test->update([
                'name' => $name,
                'sample' => $data['sample_type'],
                'msr_unit' => $data['msr_unit'] ?? '',
                'normal_range' => $data['normal_range'] ?? '',
                'isVisible' => filter_var($data['is_visible'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'modify_id' => Auth::user()?->name ?? 'system',
                'modify_time' => now(),
            ]);

            $billing->update(array_merge(
                $this->priceColumnsForUnitCost((float) $data['unit_price']),
                [
                    'item_description' => $shortBilling,
                    'item_long_description' => Str::limit(trim($data['billing_long_description'] ?? $name), 255, ''),
                    'modify_id' => Auth::user()?->name,
                    'modify_time' => now(),
                ]
            ));

            $modernCode = strtoupper(str_replace('_', '', $itemCode));
            LabTest::query()
                ->where('code', $modernCode)
                ->update([
                    'name' => $name,
                    'sample_type' => $data['sample_type'],
                    'unit_price' => round((float) $data['unit_price'], 2),
                ]);

            $after = $this->laboratoryServicePayload($itemCode);
            $this->writeAuditLog('laboratory', $itemCode, $before, $after);

            return $after;
        });
    }

    public function auditLogs(string $serviceType, string $itemCode, int $limit = 50)
    {
        return MasterServiceAuditLog::query()
            ->with('user:id,name,email')
            ->where('service_type', $serviceType)
            ->where('item_code', $itemCode)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    protected function writeAuditLog(string $serviceType, string $itemCode, array $before, array $after): void
    {
        if ($before === $after) {
            return;
        }

        MasterServiceAuditLog::create([
            'service_type' => $serviceType,
            'item_code' => $itemCode,
            'user_id' => Auth::id(),
            'before_data' => $before,
            'after_data' => $after,
            'created_at' => now(),
        ]);
    }

    protected function priceColumnsForUnitCost(float $unitCost): array
    {
        $cost = round($unitCost, 2);

        return [
            'item_unit_cost' => $cost,
            'all_inc' => $cost,
            'item_unit_cost_one' => $cost,
            'staff_grade_1' => $cost,
            'staff_grade_2' => $cost,
            'staff_grade_3' => $cost,
            'sh' => $cost,
            'lt_gen' => $cost,
            'shmd' => $cost,
            'drokia' => $cost,
            'ugpolice' => $cost,
        ];
    }
}
