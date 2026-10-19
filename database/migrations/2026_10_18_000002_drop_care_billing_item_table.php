<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('care_billing_item');
    }

    public function down(): void
    {
        // Table recreated by 2026_10_18_000001 if rolled back in order.
    }
};
