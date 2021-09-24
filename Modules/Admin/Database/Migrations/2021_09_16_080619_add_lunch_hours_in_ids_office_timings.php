<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLunchHoursInIdsOfficeTimings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_office_timings', function (Blueprint $table) {
            $table->time('lunch_start_time')->nullable()->after('updated_by');
            $table->time('lunch_end_time')->nullable()->after('lunch_start_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ids_office_timings', function (Blueprint $table) {
            $table->dropColumn('lunch_start_time');
            $table->dropColumn('lunch_end_time');
        });
    }
}
