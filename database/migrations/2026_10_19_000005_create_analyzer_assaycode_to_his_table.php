<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyzer_assaycode_to_his', function (Blueprint $table) {
            $table->id();
            $table->string('analyzer_id', 50);
            $table->unsignedBigInteger('analyzer_assaycode_id');
            $table->string('his_test_code', 35);
            $table->timestamps();

            $table->foreign('analyzer_id')
                ->references('analyzer_id')
                ->on('analyzer_details')
                ->cascadeOnDelete();

            $table->foreign('analyzer_assaycode_id')
                ->references('id')
                ->on('analyzer_assaycode_details')
                ->cascadeOnDelete();

            $table->unique(['analyzer_assaycode_id', 'his_test_code'], 'assay_his_unique');
            $table->index('his_test_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyzer_assaycode_to_his');
    }
};
