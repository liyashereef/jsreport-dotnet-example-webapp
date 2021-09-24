<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMobileSettingsTableToAddFieldTripShowDistance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('mobile_app_settings',function($table){

            $table->double('trip_show_distance')->nullable()->after('trip_show_speed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('mobile_app_settings',function($table){

            $table->dropColumn('trip_show_distance');
        });
    }
}
