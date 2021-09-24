<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerTableGeofence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('basement_mode')->after('time_shift_enabled')->nullable(); 
            $table->string('basement_interval')->after('basement_mode')->nullable(); 
            $table->unsignedInteger('basement_noofrounds')->after('basement_interval')->nullable();
            $table->boolean('geo_fence')->after('basement_interval')->default(true)->nullable();
            $table->boolean('mobile_security_patrol_site')->after('geo_fence')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('basement_mode');
            $table->dropColumn('basement_interval');
            $table->dropColumn('basement_noofrounds');
            $table->dropColumn('geo_fence');
            $table->dropColumn('mobile_security_patrol_site');
        });
    }
}
