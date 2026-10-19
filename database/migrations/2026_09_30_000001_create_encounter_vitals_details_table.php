<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounter_vitals_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->json('vitals_details');
            $table->timestamp('recorded_at');
            $table->foreignId('saved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['visit_id', 'recorded_at']);
        });

        if (Schema::hasTable('visit_vitals')) {
            $legacyRows = DB::table('visit_vitals')->orderBy('id')->get();

            foreach ($legacyRows as $row) {
                DB::table('encounter_vitals_details')->insert([
                    'visit_id' => $row->visit_id,
                    'vitals_details' => json_encode([
                        'blood_pressure' => $row->blood_pressure,
                        'pulse' => $row->pulse,
                        'temperature' => $row->temperature,
                        'respiratory_rate' => $row->respiratory_rate,
                        'oxygen_saturation' => $row->oxygen_saturation,
                        'weight' => $row->weight,
                        'height' => $row->height,
                        'notes' => $row->notes,
                    ]),
                    'recorded_at' => $row->recorded_at,
                    'saved_by' => null,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('encounter_vitals_details');
    }
};
