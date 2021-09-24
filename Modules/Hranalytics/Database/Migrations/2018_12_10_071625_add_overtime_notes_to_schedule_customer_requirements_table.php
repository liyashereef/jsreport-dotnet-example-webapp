<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOvertimeNotesToScheduleCustomerRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_customer_requirements', function (Blueprint $table) {
            $table->text('overtime_notes')->nullable()->after('notes');
            $table->integer('no_of_shifts')->nullable()->after('length_of_shift');
            $table->string('time_scheduled', 255)->nullable()->change();
            $table->string('length_of_shift', 255)->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_customer_requirements', function (Blueprint $table) {
            $table->dropColumn('overtime_notes');
            $table->dropColumn('no_of_shifts');
            $table->string('time_scheduled')->nullable(false)->change();
            $table->string('length_of_shift')->nullable(false)->change();
        });
    }
}
