<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_test_param', function (Blueprint $table) {
            $table->string('item_code', 35)->primary();
            $table->unsignedTinyInteger('is_profile')->default(0);
            $table->timestamps();
        });

        Schema::create('lab_test_profile_child', function (Blueprint $table) {
            $table->id();
            $table->string('parent_item_code', 35);
            $table->string('child_item_code', 35);
            $table->unsignedSmallInteger('sort_nr')->default(0);

            $table->index('parent_item_code');
            $table->index('child_item_code');
            $table->unique(['parent_item_code', 'child_item_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_test_profile_child');
        Schema::dropIfExists('lab_test_param');
    }
};
