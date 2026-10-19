<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuItems = [
            [
                'name' => 'Dashboard',
                'icon' => 'fas fa-fw fa-tachometer-alt',
                'route' => 'dashboard',
                'parent_id' => 0,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Appointments',
                'icon' => 'fas fa-fw fa-calendar-alt',
                'route' => 'appointments.index',
                'parent_id' => 0,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Patients',
                'icon' => 'fas fa-fw fa-user-injured',
                'route' => 'patients.index',
                'parent_id' => 0,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Billing',
                'icon' => 'fas fa-fw fa-file-invoice-dollar',
                'route' => 'billing.index',
                'parent_id' => 0,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Laboratory',
                'icon' => 'fas fa-fw fa-flask',
                'route' => 'laboratory.index',
                'parent_id' => 0,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Diagnosis',
                'icon' => 'fas fa-fw fa-diagnoses',
                'route' => 'diagnosis.index',
                'parent_id' => 0,
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Reports',
                'icon' => 'fas fa-fw fa-chart-bar',
                'route' => 'reports.index',
                'parent_id' => 0,
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Master',
                'icon' => 'fas fa-fw fa-cogs',
                'route' => 'master.index',
                'parent_id' => 0,
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($menuItems as $item) {
            MenuItem::updateOrCreate(
                ['route' => $item['route']],
                $item
            );
        }

        $this->command->info('Menu items seeded successfully.');
    }
}