<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmployeeAvailabilitiesTableAddCreatedByColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_availabilities', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('shift_timing_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_availabilities', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
}
