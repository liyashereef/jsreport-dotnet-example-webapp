<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFontColorInKpiThresholdColors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpi_threshold_colors', function (Blueprint $table) {
            $table->string("font_color")->nullable()->after("color_code");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpi_threshold_colors', function (Blueprint $table) {
            $table->dropColumn("font_color");
        });
    }
}
