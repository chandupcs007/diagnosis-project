<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('blood_pressure')->nullable();
            $table->unsignedSmallInteger('pulse')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->unsignedSmallInteger('respiratory_rate')->nullable();
            $table->decimal('oxygen_saturation', 5, 2)->nullable();
            $table->decimal('weight', 6, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        Schema::create('visit_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('diagnosis_code')->nullable();
            $table->string('diagnosis_name');
            $table->enum('severity', ['mild', 'moderate', 'severe'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('visit_chief_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->text('complaint');
            $table->string('duration')->nullable();
            $table->enum('severity', ['mild', 'moderate', 'severe'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('visit_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->enum('history_type', ['medical', 'surgical', 'family', 'social', 'allergy', 'medication']);
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('visit_billing_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('service_name');
            $table->string('service_code')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('visit_lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('test_name');
            $table->string('test_code')->nullable();
            $table->string('result_value');
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->enum('status', ['normal', 'abnormal', 'critical'])->default('normal');
            $table->text('notes')->nullable();
            $table->timestamp('tested_at')->nullable();
            $table->timestamps();
        });

        Schema::create('visit_radiology_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('study_type');
            $table->string('body_part')->nullable();
            $table->text('findings')->nullable();
            $table->text('impression')->nullable();
            $table->enum('status', ['pending', 'completed', 'reviewed'])->default('completed');
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_radiology_results');
        Schema::dropIfExists('visit_lab_results');
        Schema::dropIfExists('visit_billing_services');
        Schema::dropIfExists('visit_histories');
        Schema::dropIfExists('visit_chief_complaints');
        Schema::dropIfExists('visit_diagnoses');
        Schema::dropIfExists('visit_vitals');
    }
};
