<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveFacilityService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facility_services', function (Blueprint $table) {
            $table->boolean("restrict_booking")->default(false)->after('description');
            $table->boolean("active")->default(true)->after("restrict_booking");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facility_services', function (Blueprint $table) {
            $table->dropColumn("restrict_booking");
            $table->dropColumn("active");
        });
    }
}
