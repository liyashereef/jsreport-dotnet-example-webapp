<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScreeningEnabledInVisitorLogDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_log_devices', function (Blueprint $table) {
            $table->boolean('screening_enabled')->default(false)->after('device_id');
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
            $table->dropColumn('screening_enabled');
        });
    }
}
