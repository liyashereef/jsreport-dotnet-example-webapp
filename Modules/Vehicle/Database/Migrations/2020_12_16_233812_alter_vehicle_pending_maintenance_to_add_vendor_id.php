<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVehiclePendingMaintenanceToAddVendorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_pending_maintenance', function (Blueprint $table) {
            $table->integer('vendor_id')->after('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_pending_maintenance', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
    }
}
