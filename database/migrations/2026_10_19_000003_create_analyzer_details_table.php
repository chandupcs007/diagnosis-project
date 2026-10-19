<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyzer_details', function (Blueprint $table) {
            $table->id();
            $table->string('analyzer_name', 150);
            $table->string('analyzer_id', 50)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyzer_details');
    }
};
