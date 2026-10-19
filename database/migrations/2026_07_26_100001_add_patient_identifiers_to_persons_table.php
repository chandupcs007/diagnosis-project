<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('pid', 20)->nullable()->unique()->after('id');
            $table->string('temp_number', 20)->nullable()->unique()->after('pid');
            $table->enum('registration_status', ['temp', 'registered'])->default('registered')->after('temp_number');
            $table->timestamp('pid_generated_at')->nullable()->after('registration_status');
        });
    }

    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn(['pid', 'temp_number', 'registration_status', 'pid_generated_at']);
        });
    }
};
