<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVehiclePendingMaintenanceToDropVendorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('vehicle_pending_maintenance', 'vendor_id'))
        {
            Schema::table('vehicle_pending_maintenance', function (Blueprint $table)
            {
                $table->dropColumn('vendor_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('vehicle_pending_maintenance', 'vendor_id'))
        {
            Schema::table('vehicle_pending_maintenance', function (Blueprint $table)
            {
                $table->dropColumn('vendor_id');
            });
        }
    }
}
