<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('bill_number')->unique();
            $table->string('invoice_number')->unique();
            $table->string('receipt_number')->unique();
            $table->string('payment_method');
            $table->decimal('total_amount', 10, 2);
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at');
            $table->timestamps();
        });

        Schema::create('visit_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_bill_id')->constrained('visit_bills')->cascadeOnDelete();
            $table->foreignId('visit_billing_service_id')->nullable()->constrained('visit_billing_services')->nullOnDelete();
            $table->string('service_name');
            $table->string('service_code')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });

        Schema::table('visit_billing_services', function (Blueprint $table) {
            $table->foreignId('visit_bill_id')->nullable()->after('visit_id')->constrained('visit_bills')->nullOnDelete();
        });

        Schema::table('visit_payments', function (Blueprint $table) {
            $table->foreignId('visit_bill_id')->nullable()->after('visit_id')->constrained('visit_bills')->nullOnDelete();
        });

        $this->backfillPaidBills();
    }

    public function down(): void
    {
        Schema::table('visit_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visit_bill_id');
        });

        Schema::table('visit_billing_services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visit_bill_id');
        });

        Schema::dropIfExists('visit_bill_items');
        Schema::dropIfExists('visit_bills');
    }

    private function backfillPaidBills(): void
    {
        $payments = DB::table('visit_payments')->orderBy('id')->get();
        $sequences = [];

        foreach ($payments as $payment) {
            $services = DB::table('visit_billing_services')
                ->where('visit_id', $payment->visit_id)
                ->whereNull('visit_bill_id')
                ->orderBy('id')
                ->get();

            $remaining = round((float) $payment->amount, 2);
            $chosen = [];

            foreach ($services as $service) {
                $line = round((float) $service->total_amount, 2);
                if ($line - $remaining > 0.001) {
                    break;
                }

                $chosen[] = $service;
                $remaining = round($remaining - $line, 2);
            }

            if ($chosen === [] || $remaining > 0.01) {
                continue;
            }

            $postedAt = $payment->paid_at ?? $payment->created_at ?? now();
            $date = \Illuminate\Support\Carbon::parse($postedAt)->format('Ymd');
            $sequences[$date] = ($sequences[$date] ?? 0) + 1;
            $sequence = str_pad((string) $sequences[$date], 4, '0', STR_PAD_LEFT);

            $billId = DB::table('visit_bills')->insertGetId([
                'visit_id' => $payment->visit_id,
                'bill_number' => 'BILL' . $date . $sequence,
                'invoice_number' => 'INV' . $date . $sequence,
                'receipt_number' => 'RCT' . $date . $sequence,
                'payment_method' => $payment->payment_method,
                'total_amount' => $payment->amount,
                'reference_number' => $payment->reference_number,
                'notes' => $payment->notes,
                'received_by' => $payment->received_by,
                'posted_at' => $postedAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($chosen as $service) {
                DB::table('visit_bill_items')->insert([
                    'visit_bill_id' => $billId,
                    'visit_billing_service_id' => $service->id,
                    'service_name' => $service->service_name,
                    'service_code' => $service->service_code,
                    'quantity' => $service->quantity,
                    'unit_price' => $service->unit_price,
                    'total_amount' => $service->total_amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('visit_billing_services')
                    ->where('id', $service->id)
                    ->update(['visit_bill_id' => $billId]);
            }

            DB::table('visit_payments')
                ->where('id', $payment->id)
                ->update(['visit_bill_id' => $billId]);
        }
    }
};
