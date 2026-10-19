<?php

namespace Database\Seeders;

use App\Models\HospitalSetting;
use App\Services\HospitalSettingService;
use Illuminate\Database\Seeder;

class HospitalSettingSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(HospitalSettingService::class);

        HospitalSetting::updateOrCreate(
            ['hospital_name' => config('diagnosis.hospital')],
            $service->defaults()
        );

        $service->clearCache();

        $this->command->info('Hospital settings seeded successfully.');
    }
}
