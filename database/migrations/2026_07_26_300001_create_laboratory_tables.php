<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('department', ['biochemistry', 'hematology', 'microbiology']);
            $table->string('sample_type');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lab_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visit_billing_service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lab_test_id')->nullable()->constrained()->nullOnDelete();
            $table->string('test_name');
            $table->string('test_code')->nullable();
            $table->enum('lab_department', ['biochemistry', 'hematology', 'microbiology']);
            $table->string('sample_type');
            $table->string('sample_number')->nullable();
            $table->enum('status', [
                'sample_acknowledgement',
                'sample_receive',
                'report_entry',
                'authorization',
                'report_data',
            ])->default('sample_acknowledgement');

            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('acknowledgement_notes')->nullable();

            $table->timestamp('received_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('receive_notes')->nullable();

            $table->string('result_value')->nullable();
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->enum('result_status', ['normal', 'abnormal', 'critical'])->nullable();
            $table->text('report_notes')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('authorized_at')->nullable();
            $table->foreignId('authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('authorization_notes')->nullable();
            $table->boolean('is_rejected')->default(false);
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index(['status', 'lab_department']);
            $table->index('sample_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_requests');
        Schema::dropIfExists('lab_tests');
    }
};
