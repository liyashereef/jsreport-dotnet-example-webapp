<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMobileSettingsTableToAddAverageSpeedLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_app_settings', function (Blueprint $table) {
            $table->integer('average_speed_limit')->nullable()->after('key_management_module_image_limit');
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
            $table->dropColumn('average_speed_limit');
        });
    }
}
