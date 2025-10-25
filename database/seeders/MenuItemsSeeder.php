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
                'name' => 'Patients',
                'icon' => 'fas fa-fw fa-user-injured',
                'route' => 'patients.index',
                'parent_id' => 0,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Diagnosis',
                'icon' => 'fas fa-fw fa-diagnoses',
                'route' => 'diagnosis.index',
                'parent_id' => 0,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Reports',
                'icon' => 'fas fa-fw fa-chart-bar',
                'route' => 'reports.index',
                'parent_id' => 0,
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($menuItems as $item) {
            // Check if menu item already exists
            $exists = MenuItem::where('name', $item['name'])
                ->where('route', $item['route'])
                ->exists();
            
            if (!$exists) {
                MenuItem::create($item);
            }
        }

        $this->command->info('Menu items seeded successfully.');
    }
}