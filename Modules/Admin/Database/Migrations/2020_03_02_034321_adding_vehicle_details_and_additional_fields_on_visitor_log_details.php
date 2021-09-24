<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingVehicleDetailsAndAdditionalFieldsOnVisitorLogDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_log_details', function (Blueprint $table) {
            $table->string('vehicle_reference',300)->nullable()->after('force_checkout');
            $table->string('work_location')->nullable()->after('vehicle_reference');
            $table->string('additional_comments',300)->nullable()->after('work_location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitor_log_details', function (Blueprint $table) {
            $table->dropColumn('vehicle_reference');
            $table->dropColumn('work_location');
            $table->dropColumn('additional_comments');
        });
    }
}
