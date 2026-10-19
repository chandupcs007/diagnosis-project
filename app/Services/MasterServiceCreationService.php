<?php

namespace App\Services;

use App\Models\BillingMaster;
use App\Models\HospitalService;
use App\Models\HospitalServiceCategory;
use App\Models\LabTest;
use App\Models\LegacyLabTest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterServiceCreationService
{
    public function hospitalCategories()
    {
        return HospitalServiceCategory::query()
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function nextHospitalItemCode(string $categoryCode): string
    {
        $maxSuffix = HospitalService::query()
            ->where('category', $categoryCode)
            ->whereNotNull('code')
            ->pluck('code')
            ->map(fn (?string $code) => $this->hospitalCodeSuffix($code))
            ->max();

        $next = ((int) $maxSuffix) + 1;

        return $categoryCode . '-' . $next;
    }

    public function nextLaboratoryItemCode(string $department): string
    {
        $prefix = config("diagnosis.lab_legacy_id_prefixes.{$department}", '_bio');

        $maxNumber = LegacyLabTest::query()
            ->where('group_id', $department)
            ->pluck('id')
            ->map(fn (string $id) => $this->legacyLabIdNumber($id, $prefix))
            ->max();

        $next = ((int) $maxNumber) + 1;

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    public function createHospitalService(
        HospitalServiceCategory $category,
        string $serviceName,
        float $unitPrice
    ): array {
        return DB::transaction(function () use ($category, $serviceName, $unitPrice) {
            $itemCode = $this->nextHospitalItemCode($category->category);

            HospitalService::create([
                'code' => $itemCode,
                'category' => $category->category,
                'description' => $serviceName,
                'sub_level' => 0,
                'navision' => 0,
                'work_type' => '0',
            ]);

            $billing = BillingMaster::create(
                $this->billingRowPayload(
                    $itemCode,
                    $serviceName,
                    $unitPrice,
                    $category->code ?: 'HSPU',
                    0
                )
            );

            return [
                'item_code' => $itemCode,
                'billing' => $billing,
            ];
        });
    }

    public function createLaboratoryService(
        string $department,
        string $serviceName,
        float $unitPrice
    ): array {
        return DB::transaction(function () use ($department, $serviceName, $unitPrice) {
            $itemCode = $this->nextLaboratoryItemCode($department);
            $nextNr = ((int) LegacyLabTest::query()->max('nr')) + 1;
            $userName = Auth::user()?->name ?? 'system';
            $now = now();

            LegacyLabTest::create([
                'nr' => $nextNr,
                'group_id' => $department,
                'name' => $serviceName,
                'id' => $itemCode,
                'msr_unit' => '',
                'normal_range' => '',
                'isVisible' => true,
                'ListTest' => '',
                'spaw' => '0',
                'sort_nr' => 0,
                'status' => '',
                'modify_id' => $userName,
                'modify_time' => $now,
                'create_id' => $userName,
                'create_time' => $now,
                'landscape' => 0,
                'individual_batchnr' => 0,
                'tube_color' => '0',
                'dept' => 'lab',
                'dept1' => 'lab',
                'isqrcode' => 1,
                'modality' => '0',
            ]);

            $deptNr = (int) config("diagnosis.lab_billing_dept_nr.{$department}", 22);

            $billing = BillingMaster::create(
                $this->billingRowPayload(
                    $itemCode,
                    $serviceName,
                    $unitPrice,
                    'LT',
                    $deptNr
                )
            );

            $this->syncModernLabTest($department, $serviceName, $itemCode, $unitPrice);

            $modernCode = strtoupper(str_replace('_', '', $itemCode));
            $labTestId = LabTest::query()->where('code', $modernCode)->value('id');

            return [
                'item_code' => $itemCode,
                'billing' => $billing,
                'lab_test_id' => $labTestId,
                'department' => $department,
            ];
        });
    }

    public function billingCatalog()
    {
        return BillingMaster::query()
            ->where('isvisible', '1')
            ->orderBy('item_description')
            ->orderBy('item_code')
            ->get();
    }

    public function resolveLabFromBillingItem(BillingMaster $item): ?array
    {
        $itemType = strtoupper(trim((string) $item->item_type));
        $isLabItem = $itemType === 'LT' || str_starts_with($item->item_code, '_');

        if (!$isLabItem) {
            return null;
        }

        $legacy = LegacyLabTest::query()->find($item->item_code);
        $department = $legacy?->group_id;

        if (!$department || !array_key_exists($department, config('diagnosis.lab_departments', []))) {
            $department = 'biochemistry';
        }

        $modernCode = strtoupper(str_replace('_', '', $item->item_code));
        $labTestId = LabTest::query()
            ->where('code', $modernCode)
            ->orWhere('code', $item->item_code)
            ->value('id');

        return [
            'department' => $department,
            'sample_type' => 'Serum',
            'lab_test_id' => $labTestId,
        ];
    }

    public function billingRowPayload(
        string $itemCode,
        string $description,
        float $unitCost,
        string $itemType,
        int $deptNr
    ): array {
        $cost = round($unitCost, 2);
        $short = Str::limit($description, 100, '');
        $userName = Auth::user()?->name;
        $now = now();

        return [
            'item_code' => $itemCode,
            'item_description' => $short,
            'item_long_description' => $short,
            'item_unit_cost' => $cost,
            'usd' => 0,
            'un' => 0,
            'cigna' => 0,
            'all_inc' => $cost,
            'item_unit_cost_one' => $cost,
            'jubilee' => 0,
            'staff_grade_1' => $cost,
            'staff_grade_2' => $cost,
            'staff_grade_3' => $cost,
            'liberty' => 0,
            'avanti_usd' => 0,
            'maia' => 0,
            'lweaz' => 0,
            'stanbic' => 0,
            'updf' => 0,
            'aar' => 0,
            'sanlam' => 0,
            'henner' => 0,
            'sos' => 0,
            'iom' => 0,
            'sccn' => 0,
            'sh' => $cost,
            'lt_gen' => $cost,
            'shmd' => $cost,
            'drokia' => $cost,
            'ugpolice' => $cost,
            'ame' => 0,
            'wellness' => 0,
            'item_type' => $itemType,
            'dept_nr' => $deptNr,
            'item_discount_max_allowed' => 0,
            'isvisible' => '1',
            'code_search' => '0',
            'code_packnr' => '0',
            'opip' => 0,
            'shift' => 0,
            'packsort_nr' => 0,
            'iszero' => false,
            'modality_code' => '0',
            'account_no' => '4010',
            'dept_code' => 'm',
            'is_outsourced' => 0,
            'navision' => 1,
            'create_id' => $userName,
            'create_time' => $now,
            'modify_id' => $userName,
            'modify_time' => $now,
            'wardname' => '',
            'nav_code' => $itemType === 'LT' ? '522001' : '520001',
            'sanlam_item_code' => '0',
        ];
    }

    protected function hospitalCodeSuffix(?string $code): int
    {
        if (!$code || !str_contains($code, '-')) {
            return 0;
        }

        return (int) last(explode('-', $code));
    }

    protected function legacyLabIdNumber(string $id, string $prefix): int
    {
        if (!str_starts_with($id, $prefix)) {
            return 0;
        }

        $numeric = substr($id, strlen($prefix));

        return ctype_digit($numeric) ? (int) $numeric : 0;
    }

    protected function syncModernLabTest(
        string $department,
        string $serviceName,
        string $legacyItemCode,
        float $unitPrice
    ): void {
        if (!in_array($department, ['biochemistry', 'hematology', 'microbiology'], true)) {
            return;
        }

        $modernCode = strtoupper(str_replace('_', '', $legacyItemCode));

        LabTest::updateOrCreate(
            ['code' => $modernCode],
            [
                'name' => $serviceName,
                'department' => $department,
                'sample_type' => 'Serum',
                'unit_price' => $unitPrice,
                'is_active' => true,
            ]
        );
    }
}
