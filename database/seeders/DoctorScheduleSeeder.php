<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $slotDurations = [
            'Cardiology' => 20,
            'Neurology' => 30,
            'Orthopedics' => 20,
            'Pediatrics' => 15,
            'Dermatology' => 15,
            'Ophthalmology' => 20,
            'Dentistry' => 30,
            'General Medicine' => 15,
        ];

        $weekdays = [0, 1, 2, 3, 4, 5, 6]; // Sunday to Saturday

        User::doctors()->each(function ($doctor) use ($slotDurations, $weekdays) {
            $duration = $slotDurations[$doctor->specialization] ?? 15;

            foreach ($weekdays as $day) {
                $isWeekend = in_array($day, [0, 6], true);

                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => '09:00:00',
                        'end_time' => $isWeekend ? '13:00:00' : '17:00:00',
                        'slot_duration_minutes' => $duration,
                        'is_active' => true,
                    ]
                );
            }
        });

        $this->command->info('Doctor schedules seeded successfully.');
    }
}
