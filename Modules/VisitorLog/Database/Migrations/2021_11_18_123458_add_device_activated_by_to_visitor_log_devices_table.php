<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeviceActivatedByToVisitorLogDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_log_devices', function (Blueprint $table) {
            $table->integer('activated_by')->nullable()->after('activated_at')->comment('User table');
        });
        Schema::table('visitor_log_device_settings', function (Blueprint $table) {
            $table->dropColumn('device_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitor_log_devices', function (Blueprint $table) {
            $table->dropColumn('activated_by');
        });
        Schema::table('visitor_log_device_settings', function (Blueprint $table) {
            $table->string('device_id')->nullable();
        });

    }
}
