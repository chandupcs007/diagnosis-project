<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_service_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service_type', 20);
            $table->string('item_code', 35);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('before_data');
            $table->json('after_data');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['service_type', 'item_code']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_service_audit_logs');
    }
};
