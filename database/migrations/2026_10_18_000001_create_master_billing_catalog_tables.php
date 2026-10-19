<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_service_category', function (Blueprint $table) {
            $table->unsignedTinyInteger('nr')->primary();
            $table->string('category', 35)->default('');
            $table->string('name', 35)->default('');
            $table->string('LD_var', 35)->default('');
            $table->string('code', 25)->default('');
            $table->string('description', 255)->default('');
            $table->tinyInteger('multidept')->default(0);
            $table->string('hide_from', 255)->default('0');
            $table->string('status', 25)->nullable();
            $table->text('history')->nullable();
            $table->string('modify_id', 35)->nullable();
            $table->timestamp('modify_time')->useCurrent()->useCurrentOnUpdate();
            $table->string('create_id', 35)->nullable();
            $table->timestamp('create_time')->nullable();
            $table->integer('navision')->default(0);
            $table->tinyInteger('service_type')->default(1);

            $table->index('category');
            $table->index('multidept');
        });

        Schema::create('hospital_service', function (Blueprint $table) {
            $table->string('code', 35)->nullable()->index();
            $table->string('category', 10)->default('')->index();
            $table->text('description');
            $table->text('inclusive')->nullable();
            $table->text('exclusive')->nullable();
            $table->text('notes')->nullable();
            $table->char('std_code', 1)->nullable();
            $table->tinyInteger('sub_level')->default(0);
            $table->text('remarks')->nullable();
            $table->integer('navision')->default(0);
            $table->string('work_type', 20)->default('0');
        });

        Schema::create('billing_master', function (Blueprint $table) {
            $table->string('item_code', 50)->primary();
            $table->string('item_description', 100)->nullable();
            $table->string('item_long_description', 100)->default('');
            $table->float('item_unit_cost', 10, 2)->default(0);
            $table->float('usd', 10, 2)->default(0);
            $table->float('un', 10, 2)->default(0);
            $table->float('cigna', 10, 2)->default(0);
            $table->float('all_inc', 10, 2)->default(0);
            $table->float('item_unit_cost_one', 10, 2)->default(0);
            $table->float('jubilee', 10, 2)->default(0);
            $table->float('staff_grade_1', 10, 2)->default(0);
            $table->float('staff_grade_2', 10, 2)->default(0);
            $table->float('staff_grade_3', 10, 2)->default(0);
            $table->float('liberty', 10, 2)->default(0);
            $table->float('avanti_usd', 10, 2)->default(0);
            $table->float('maia', 10, 2)->default(0);
            $table->float('lweaz', 10, 2)->default(0);
            $table->float('stanbic', 10, 2)->default(0);
            $table->float('updf', 10, 2)->default(0);
            $table->float('aar', 10, 2)->default(0);
            $table->float('sanlam', 10, 2)->default(0);
            $table->float('henner', 10, 2)->default(0);
            $table->float('sos', 10, 2)->default(0);
            $table->float('iom', 10, 2)->default(0);
            $table->float('sccn', 10, 2)->default(0);
            $table->float('sh', 10, 2)->default(0);
            $table->float('lt_gen', 10, 2)->default(0);
            $table->float('shmd', 10, 2)->default(0);
            $table->float('drokia', 10, 2)->default(0);
            $table->float('ugpolice', 10, 2)->default(0);
            $table->float('ame', 10, 2)->default(0);
            $table->float('wellness', 10, 2)->default(0);
            $table->text('item_type')->nullable();
            $table->integer('dept_nr')->default(0);
            $table->unsignedTinyInteger('item_discount_max_allowed')->default(0);
            $table->string('isvisible', 5)->default('1');
            $table->longText('code_search')->nullable();
            $table->string('code_packnr', 40)->default('');
            $table->integer('opip')->default(0);
            $table->integer('shift')->default(0);
            $table->integer('packsort_nr')->default(0);
            $table->boolean('iszero')->default(false);
            $table->string('modality_code', 25)->default('');
            $table->string('account_no', 50)->default('0');
            $table->string('dept_code', 50)->default('0');
            $table->tinyInteger('is_outsourced')->default(0);
            $table->integer('navision')->nullable();
            $table->string('create_id', 35)->nullable();
            $table->timestamp('create_time')->nullable();
            $table->string('modify_id', 35)->nullable();
            $table->timestamp('modify_time')->nullable();
            $table->string('sys_ip_addr', 50)->nullable();
            $table->string('wardname', 255)->default('');
            $table->string('nav_code', 55)->default('');
            $table->string('sanlam_item_code', 20)->default('0');

            $table->index('dept_nr');
            $table->index('isvisible');
            $table->index('item_description');
            $table->index('item_long_description');
            $table->index('item_unit_cost');
            $table->index('wardname');
        });

        Schema::create('lab_test', function (Blueprint $table) {
            $table->unsignedSmallInteger('nr')->unique();
            $table->string('group_id', 35)->default('');
            $table->string('name', 250)->default('');
            $table->string('id', 35)->primary();
            $table->string('msr_unit', 80)->default('');
            $table->longText('median')->nullable();
            $table->string('hi_bound', 20)->nullable();
            $table->string('lo_bound', 20)->nullable();
            $table->text('normal_range')->nullable();
            $table->longText('sample')->nullable();
            $table->longText('pagesample')->nullable();
            $table->longText('methodology')->nullable();
            $table->longText('hi_critical')->nullable();
            $table->string('lo_critical', 20)->nullable();
            $table->string('hi_toxic', 20)->nullable();
            $table->string('lo_toxic', 20)->nullable();
            $table->boolean('isVisible')->default(true);
            $table->longText('child')->nullable();
            $table->integer('isText')->default(0);
            $table->integer('isList')->default(0);
            $table->text('ListTest')->nullable();
            $table->char('spaw', 1)->default('0');
            $table->integer('sort_nr')->default(0);
            $table->string('status', 25)->default('');
            $table->longText('history')->nullable();
            $table->string('modify_id', 35)->default('');
            $table->timestamp('modify_time')->nullable();
            $table->string('create_id', 35)->default('');
            $table->timestamp('create_time')->nullable();
            $table->integer('landscape')->default(0);
            $table->integer('individual_batchnr')->default(0);
            $table->string('tube_color', 60)->default('0');
            $table->text('interpretation')->nullable();
            $table->string('sys_ip_addr', 50)->nullable();
            $table->string('dept', 35)->nullable();
            $table->string('dept1', 35)->default('');
            $table->integer('isqrcode')->default(1);
            $table->string('modality', 4)->default('0');

            $table->index('group_id');
            $table->index('isVisible');
            $table->index('dept');
            $table->index('dept1');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('lab_test');
        Schema::dropIfExists('billing_master');
        Schema::dropIfExists('hospital_service');
        Schema::dropIfExists('hospital_service_category');
    }
};
