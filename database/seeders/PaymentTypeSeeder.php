<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $locationId = (int) config('diagnosis.billing.location_id', 10);

        $rows = [
            ['type' => 3, 'description' => 'OP Invoice', 'no' => 1388761],
            ['type' => 4, 'description' => 'OP Receipt No', 'no' => 2488211],
            ['type' => 38, 'description' => 'Bill Chart No', 'no' => 201572100],
        ];

        foreach ($rows as $row) {
            PaymentType::updateOrCreate(
                ['type' => $row['type']],
                [
                    'description' => $row['description'],
                    'no' => $row['no'],
                    'location_id' => $locationId,
                ]
            );
        }

        $this->command->info('Payment type sequences seeded successfully.');
    }
}
