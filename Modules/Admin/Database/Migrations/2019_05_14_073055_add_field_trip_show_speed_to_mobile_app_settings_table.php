<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldTripShowSpeedToMobileAppSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_app_settings', function (Blueprint $table) {

            $table->integer('trip_show_speed')->after('speed_limit')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_app_settings', function (Blueprint $table) {
            $table->dropColumn('trip_show_speed');

        });
    }
}
