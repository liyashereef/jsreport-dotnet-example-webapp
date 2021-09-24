<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpareBonusModelSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spare_bonus_model_settings', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger("reliability_grace_period_in_days");
            $table->string("reliability_grace_period_color_code")->default("#FFFF00");
            $table->string("reliability_grace_period_font_color_code")->default("#000000");

            $table->unsignedInteger("reliability_alert_period_in_days");
            $table->string("reliability_alert_period_color_code")->default("#FF0000");
            $table->string("reliability_alert_period_font_color_code")->default("#ffffff");

            $table->unsignedInteger("reliability_safe_score");
            $table->string("reliability_safe_score_color_code")->default("#008000");
            $table->string("reliability_safe_score_font_color_code")->default("#ffffff");

            $table->unsignedInteger("reliability_rank_top_level");
            $table->string("reliability_rank_top_level_color_code")->default("#008000");
            $table->string("reliability_rank_top_level_font_color_code")->default("#ffffff");

            $table->unsignedInteger("reliability_rank_average_level");
            $table->string("reliability_rank_average_level_color_code")->default("#FFFF00");
            $table->string("reliability_rank_average_level_font_color_code")->default("#ffffff");

            $table->string("schedule_top_rank_message")->nullable();
            $table->string("schedule_average_rank_message")->nullable();
            $table->string("schedule_below_average_rank_message")->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spare_bonus_model_settings');
    }
}
