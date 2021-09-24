<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserEmergencyContactAddPhoneNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_emergency_contacts', function (Blueprint $table) {
            $table->string("primary_phoneno")->after("full_address")->nullable();
            $table->string("alternate_phoneno")->after("primary_phoneno")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('user_emergency_contacts', function (Blueprint $table) {
            $table->dropColumn("primary_phoneno");
            $table->dropColumn("alternate_phoneno");
        });
    }
}
