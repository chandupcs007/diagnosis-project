<?php

namespace Database\Seeders;

use App\Models\BillingMaster;
use App\Models\HospitalService;
use App\Models\HospitalServiceCategory;
use Illuminate\Database\Seeder;

class HospitalServiceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        HospitalServiceCategory::updateOrCreate(
            ['nr' => 1],
            [
                'category' => '101',
                'name' => 'CONSULTATION CHARGES',
                'LD_var' => '',
                'code' => 'HSPU',
                'description' => 'CONSULTATION CHARGES',
                'multidept' => 0,
                'hide_from' => '0',
                'status' => '',
                'navision' => 1,
                'service_type' => 1,
            ]
        );

        $samples = [
            ['101-1', '101', 'SENIOR HEALTH OFFICER - PAED'],
            ['101-2', '101', 'ADMISSION AT ACCIDENT AND EMERGENCY'],
            ['101-3', '101', 'Physiotherapy-Offsite'],
        ];

        foreach ($samples as [$code, $category, $description]) {
            HospitalService::updateOrCreate(
                ['code' => $code],
                [
                    'category' => $category,
                    'description' => $description,
                    'navision' => 0,
                    'work_type' => '0',
                    'sub_level' => 0,
                ]
            );

            BillingMaster::updateOrCreate(
                ['item_code' => $code],
                array_merge(
                    app(\App\Services\MasterServiceCreationService::class)
                        ->billingRowPayload($code, $description, 50000, 'HSPU', 0),
                    ['navision' => 1]
                )
            );
        }

        $this->command->info('Hospital service catalog seeded successfully.');
    }
}
