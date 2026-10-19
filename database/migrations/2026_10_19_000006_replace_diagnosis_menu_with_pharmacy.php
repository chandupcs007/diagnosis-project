<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('menu_items')
            ->where('route', 'diagnosis.index')
            ->update([
                'name' => 'Pharmacy',
                'icon' => 'fas fa-fw fa-pills',
                'route' => 'pharmacy.index',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('menu_items')
            ->where('route', 'pharmacy.index')
            ->update([
                'name' => 'Diagnosis',
                'icon' => 'fas fa-fw fa-diagnoses',
                'route' => 'diagnosis.index',
                'updated_at' => now(),
            ]);
    }
};
