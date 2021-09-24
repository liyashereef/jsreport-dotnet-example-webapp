<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeeSchedulesAddVariance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->dropColumn('week_1_total');
            $table->dropColumn('week_2_total');
            $table->dropColumn('biweekly_total');
            $table->decimal('contractual_hours',10,2)->default(0)->after('initial_schedule_id')->nullable();
            $table->decimal('avgworkhours',10,2)->default(0)->after('contractual_hours');
            $table->decimal('variance',10,2)->default(0)->after('avgworkhours');
            $table->boolean('schedindicator')->default(true)->after('variance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->decimal('week_1_total',10,2);
            $table->decimal('week_2_total',10,2);
            $table->decimal('biweekly_total',10,2);
            $table->dropColumn('contractual_hours');
            $table->dropColumn('avgworkhours');
            $table->dropColumn('variance');
            $table->dropColumn('schedindicator');
        });
    }
}
