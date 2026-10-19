<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyzer_assaycode_details', function (Blueprint $table) {
            $table->id();
            $table->string('analyzer_id', 50);
            $table->string('assay_code', 100);
            $table->timestamps();

            $table->foreign('analyzer_id')
                ->references('analyzer_id')
                ->on('analyzer_details')
                ->cascadeOnDelete();

            $table->unique(['analyzer_id', 'assay_code']);
            $table->index('assay_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyzer_assaycode_details');
    }
};
