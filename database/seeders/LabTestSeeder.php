<?php

namespace Database\Seeders;

use App\Models\LabTest;
use Illuminate\Database\Seeder;

class LabTestSeeder extends Seeder
{
    public function run(): void
    {
        $tests = [
            ['name' => 'Blood Sugar (Fasting)', 'code' => 'BIO-001', 'department' => 'biochemistry', 'sample_type' => 'Serum', 'unit_price' => 150, 'unit' => 'mg/dL', 'reference_range' => '70-100'],
            ['name' => 'Lipid Profile', 'code' => 'BIO-002', 'department' => 'biochemistry', 'sample_type' => 'Serum', 'unit_price' => 450, 'unit' => 'mg/dL', 'reference_range' => 'Varies'],
            ['name' => 'Liver Function Test', 'code' => 'BIO-003', 'department' => 'biochemistry', 'sample_type' => 'Serum', 'unit_price' => 600, 'unit' => 'U/L', 'reference_range' => 'Varies'],
            ['name' => 'Kidney Function Test', 'code' => 'BIO-004', 'department' => 'biochemistry', 'sample_type' => 'Serum', 'unit_price' => 550, 'unit' => 'mg/dL', 'reference_range' => 'Varies'],
            ['name' => 'Complete Blood Count (CBC)', 'code' => 'HEM-001', 'department' => 'hematology', 'sample_type' => 'Blood', 'unit_price' => 300, 'unit' => 'cells/µL', 'reference_range' => 'Varies'],
            ['name' => 'Hemoglobin', 'code' => 'HEM-002', 'department' => 'hematology', 'sample_type' => 'Blood', 'unit_price' => 120, 'unit' => 'g/dL', 'reference_range' => '12-16'],
            ['name' => 'ESR', 'code' => 'HEM-003', 'department' => 'hematology', 'sample_type' => 'Blood', 'unit_price' => 100, 'unit' => 'mm/hr', 'reference_range' => '0-20'],
            ['name' => 'Peripheral Smear', 'code' => 'HEM-004', 'department' => 'hematology', 'sample_type' => 'Blood', 'unit_price' => 250, 'unit' => null, 'reference_range' => null],
            ['name' => 'Urine Culture', 'code' => 'MIC-001', 'department' => 'microbiology', 'sample_type' => 'Urine', 'unit_price' => 400, 'unit' => null, 'reference_range' => 'No growth'],
            ['name' => 'Blood Culture', 'code' => 'MIC-002', 'department' => 'microbiology', 'sample_type' => 'Blood', 'unit_price' => 800, 'unit' => null, 'reference_range' => 'No growth'],
            ['name' => 'Stool Culture', 'code' => 'MIC-003', 'department' => 'microbiology', 'sample_type' => 'Stool', 'unit_price' => 350, 'unit' => null, 'reference_range' => 'Normal flora'],
            ['name' => 'Sputum AFB', 'code' => 'MIC-004', 'department' => 'microbiology', 'sample_type' => 'Sputum', 'unit_price' => 200, 'unit' => null, 'reference_range' => 'Negative'],
        ];

        foreach ($tests as $test) {
            LabTest::updateOrCreate(['code' => $test['code']], array_merge($test, ['is_active' => true]));
        }

        $this->command->info('Lab tests seeded successfully.');
    }
}
