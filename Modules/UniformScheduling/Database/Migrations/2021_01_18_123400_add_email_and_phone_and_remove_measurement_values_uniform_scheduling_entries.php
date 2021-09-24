<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailAndPhoneAndRemoveMeasurementValuesUniformSchedulingEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniform_scheduling_entries', function (Blueprint $table) {
            $table->dropColumn(['neck_size', 'chest_size', 'waist_size','inside_leg_size','hip_size']);
            $table->string('email')->nullable()->after('user_id');
            $table->string('phone_number')->nullable()->after('email');
            $table->smallInteger('gender')->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniform_scheduling_entries', function (Blueprint $table) {
            $table->string('neck_size')->nullable();
            $table->string('chest_size')->nullable();
            $table->string('waist_size')->nullable();
            $table->string('inside_leg_size')->nullable();
            $table->string('hip_size')->nullable();
            $table->dropColumn(['email', 'phone_number','gender']);
        });
    }
}
