<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStcThresholdSettingsTableAddHours extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stc_threshold_settings', function (Blueprint $table) {
            $table->integer('stc_threshold_hours')->default(8);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stc_threshold_settings', function (Blueprint $table) {
            $table->dropColumn('stc_threshold_hours');
        });
    }
}
