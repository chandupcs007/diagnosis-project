<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payment_type')) {
            Schema::create('payment_type', function (Blueprint $table) {
                $table->integer('type')->primary();
                $table->string('description', 30)->nullable();
                $table->unsignedBigInteger('no')->default(0);
                $table->unsignedSmallInteger('location_id')->nullable();
            });
        }

        if (!Schema::hasTable('visit_billing_item_details')) {
            Schema::create('visit_billing_item_details', function (Blueprint $table) {
            $table->id('bill_item_id');
            $table->unsignedBigInteger('bill_item_encounter_nr')->default(0);
            $table->string('bill_item_code', 50)->nullable();
            $table->text('bill_item_desc')->nullable();
            $table->float('bill_item_unit_cost', 10, 2)->default(0);
            $table->float('bill_item_units', 4, 1)->nullable();
            $table->float('bill_item_amount', 10, 2)->nullable();
            $table->date('bill_item_date')->nullable();
            $table->date('request_date')->nullable();
            $table->integer('bill_item_status')->default(0);
            $table->integer('bill_no')->default(0);
            $table->string('bill_request_nr', 30)->default('');
            $table->integer('type_nr')->default(0);
            $table->integer('sort_nr')->default(70);
            $table->string('insurance_nr', 25)->default('0');
            $table->string('insurance_firm_id', 25)->default('0');
            $table->unsignedTinyInteger('insurance_class_nr')->default(0);
            $table->float('bill_item_final_amount', 10, 2)->default(0);
            $table->integer('doctor_nr')->default(0);
            $table->string('status', 25)->default('');
            $table->string('modify_id', 35)->default('');
            $table->timestamp('modify_time')->useCurrent()->useCurrentOnUpdate();
            $table->string('create_id', 35)->default('');
            $table->timestamp('create_time')->nullable();
            $table->integer('sub_item')->default(0);
            $table->string('bill_ward_id', 60)->nullable();
            $table->tinyInteger('doctor_payment')->default(0);
            $table->tinyInteger('bill_item_exclusion')->default(0);
            $table->date('close_date')->nullable();
            $table->tinyInteger('f_request')->default(0);
            $table->tinyInteger('f_approve')->default(0);
            $table->text('history')->nullable();

            $table->unsignedBigInteger('ex_units')->default(0);
            $table->tinyInteger('bill_item_exe')->default(0);
            $table->tinyInteger('ex_item')->default(0);
            $table->tinyInteger('inc_item')->default(0);
            $table->tinyInteger('ex_dis')->default(0);
            $table->tinyInteger('auto_final')->default(0);

            $table->index('bill_item_encounter_nr');
            $table->index('bill_no');
            $table->index('bill_item_code');
            });
        }

        if (!Schema::hasTable('visit_billing_payment')) {
            Schema::create('visit_billing_payment', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('payment_encounter_nr')->default(0);
            $table->string('payment_receipt_no', 50)->default('0');
            $table->unsignedBigInteger('bill_no')->default(0);
            $table->date('payment_date')->nullable();
            $table->time('payment_time')->default('00:00:00');
            $table->float('payment_cash_amount', 15, 2)->default(0);
            $table->string('payment_cheque_no', 50)->default('0');
            $table->double('payment_cheque_amount', 10, 2)->default(0);
            $table->string('payment_cheque_bank', 255)->default('');
            $table->string('payment_creditcard_no', 25)->default('0');
            $table->float('payment_creditcard_amount', 10, 2)->default(0);
            $table->string('payment_creditcard_bank', 255)->default('');
            $table->string('payment_mtn_no', 15)->default('0');
            $table->double('payment_mtn_amt', 10, 2)->default(0);
            $table->string('payment_mtn_bank', 255)->nullable();
            $table->string('payment_airtel_no', 15)->default('0');
            $table->double('payment_airtel_amt', 10, 2)->nullable();
            $table->string('payment_ecs_type', 50)->default('');
            $table->double('payment_amount_total', 15, 2)->default(0);
            $table->tinyInteger('final_settlement')->default(0);
            $table->string('status', 25)->default('');
            $table->text('history')->nullable();
            $table->string('modify_id', 35)->default('');
            $table->timestamp('modify_time')->useCurrent()->useCurrentOnUpdate();
            $table->string('create_id', 35)->default('');
            $table->timestamp('create_time')->nullable();
            $table->tinyInteger('auto_final')->default(0);

            $table->index('payment_encounter_nr');
            $table->index('payment_receipt_no');
            $table->index('bill_no');
            });
        }

        if (!Schema::hasTable('visit_billing_final')) {
            Schema::create('visit_billing_final', function (Blueprint $table) {
            $table->string('final_id', 50);
            $table->unsignedBigInteger('final_encounter_nr')->default(0);
            $table->integer('bill_no')->nullable();
            $table->date('final_date')->nullable();
            $table->time('final_time')->default('00:00:00');
            $table->float('final_total_bill_amount', 15, 2)->nullable();
            $table->float('final_discount', 10, 2)->nullable();
            $table->float('exclusion_discount', 10, 2)->default(0);
            $table->float('final_total_receipt_amount', 10, 2)->nullable();
            $table->float('final_amount_due', 15, 2)->nullable();
            $table->float('final_amount_recieved', 10, 2)->nullable();
            $table->double('final_sub_amt')->default(0);
            $table->string('received_from_donor', 100)->default('');
            $table->tinyInteger('isprinted')->default(0);
            $table->string('status', 10)->default('');
            $table->string('create_id', 60)->default('');
            $table->timestamp('create_time')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('fst')->default(0);
            $table->integer('navision')->default(0);
            $table->float('round_up')->default(0);
            $table->tinyInteger('auto_final')->default(0);

            $table->primary(['final_id', 'final_encounter_nr']);
            $table->index('bill_no');
            $table->index('final_encounter_nr');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_billing_final');
        Schema::dropIfExists('visit_billing_payment');
        Schema::dropIfExists('visit_billing_item_details');
        Schema::dropIfExists('payment_type');
    }
};
