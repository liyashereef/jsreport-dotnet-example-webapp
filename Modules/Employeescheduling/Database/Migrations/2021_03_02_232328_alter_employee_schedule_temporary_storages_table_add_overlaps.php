<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmployeeScheduleTemporaryStoragesTableAddOverlaps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_schedule_temporary_storages', function (Blueprint $table) {
            $table->boolean('overlaps')->default(false)->after('endtime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_schedule_temporary_storages', function (Blueprint $table) {
            $table->dropColumn('overlaps');
        });
    }
}
