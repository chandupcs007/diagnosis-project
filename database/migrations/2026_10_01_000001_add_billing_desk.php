<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->string('payer_type')->default('self_pay')->after('status');
        });

        Schema::create('visit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at');
            $table->timestamps();
        });

        $exists = DB::table('menu_items')->where('route', 'billing.index')->exists();

        if (! $exists) {
            DB::table('menu_items')->insert([
                'name' => 'Billing',
                'icon' => 'fas fa-fw fa-file-invoice-dollar',
                'route' => 'billing.index',
                'parent_id' => 0,
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $orders = [
            'dashboard' => 1,
            'appointments.index' => 2,
            'patients.index' => 3,
            'billing.index' => 4,
            'laboratory.index' => 5,
            'diagnosis.index' => 6,
            'reports.index' => 7,
            'master.index' => 8,
        ];

        foreach ($orders as $route => $order) {
            DB::table('menu_items')->where('route', $route)->update([
                'order' => $order,
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('menu_items')->where('route', 'billing.index')->delete();

        Schema::dropIfExists('visit_payments');

        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn('payer_type');
        });
    }
};
