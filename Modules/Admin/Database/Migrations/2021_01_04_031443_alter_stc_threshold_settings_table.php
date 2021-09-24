<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStcThresholdSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stc_threshold_settings', function (Blueprint $table) {
            $table->string('critical_days_font_color')->nullable()->after('critical_days_color');
            $table->string('major_days_font_color')->nullable()->after('major_days_color');
            $table->string('minor_days_font_color')->nullable()->after('minor_days_color');
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
            $table->dropColumn('critical_days_font_color');
            $table->dropColumn('major_days_font_color');
            $table->dropColumn('minor_days_font_color');
        });
    }
}
