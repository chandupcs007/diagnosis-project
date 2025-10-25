<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DoctorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'Dr. Sarah Johnson',
                'email' => 'sarah.johnson@hospital.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'specialization' => 'Cardiology',
                'phone' => '+1-555-0101',
                'qualification' => 'MD, Cardiology',
                'address' => '123 Medical Center, Healthcare City',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Michael Chen',
                'email' => 'michael.chen@hospital.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'specialization' => 'Neurology',
                'phone' => '+1-555-0102',
                'qualification' => 'MD, Neurology, PhD',
                'address' => '456 Brain Center, Neuro Street',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Emily Rodriguez',
                'email' => 'emily.rodriguez@hospital.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'specialization' => 'Pediatrics',
                'phone' => '+1-555-0103',
                'qualification' => 'MD, Pediatrics',
                'address' => '789 Children Hospital, Kids Avenue',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. James Wilson',
                'email' => 'james.wilson@hospital.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'specialization' => 'Orthopedics',
                'phone' => '+1-555-0104',
                'qualification' => 'MD, Orthopedic Surgery',
                'address' => '321 Bone Center, Joint Street',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Lisa Thompson',
                'email' => 'lisa.thompson@hospital.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'specialization' => 'Dermatology',
                'phone' => '+1-555-0105',
                'qualification' => 'MD, Dermatology',
                'address' => '654 Skin Clinic, Beauty Road',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Robert Brown',
                'email' => 'robert.brown@hospital.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'specialization' => 'General Medicine',
                'phone' => '+1-555-0106',
                'qualification' => 'MD, General Medicine',
                'address' => '987 Main Hospital, General Street',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Amanda Davis',
                'email' => 'amanda.davis@hospital.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'specialization' => 'Ophthalmology',
                'phone' => '+1-555-0107',
                'qualification' => 'MD, Ophthalmology',
                'address' => '147 Vision Center, Eye Street',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. David Miller',
                'email' => 'david.miller@hospital.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'specialization' => 'Dentistry',
                'phone' => '+1-555-0108',
                'qualification' => 'DDS, Dental Surgery',
                'address' => '258 Dental Clinic, Smile Avenue',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($doctors as $doctor) {
            User::create($doctor);
        }

        $this->command->info('Sample doctors created successfully!');
        $this->command->info('All doctors have password: password123');
    }
}