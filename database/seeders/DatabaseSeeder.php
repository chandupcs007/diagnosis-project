<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MenuItemsSeeder::class,
            HospitalSettingSeeder::class,
            DoctorsSeeder::class,
            DoctorScheduleSeeder::class,
            LabTestSeeder::class,
            HospitalServiceCatalogSeeder::class,
            PaymentTypeSeeder::class,
        ]);
    }
}