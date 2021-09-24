<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalChargesVehicleMaintenaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_maintenance_records', function (Blueprint $table) {
            $table->decimal('total_charges', 15, 2)->nullable()->after('notes');
            $table->decimal('tax', 8, 2)->nullable()->after('total_charges');
            $table->decimal('tax_amount', 15, 2)->nullable()->after('tax');
            $table->decimal('subtotal', 15, 2)->nullable()->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_maintenance_records', function (Blueprint $table) {
              $table->dropColumn('total_charges');
              $table->dropColumn('tax');
              $table->dropColumn('tax_amount');
              $table->dropColumn('subtotal');
        });
    }
}
