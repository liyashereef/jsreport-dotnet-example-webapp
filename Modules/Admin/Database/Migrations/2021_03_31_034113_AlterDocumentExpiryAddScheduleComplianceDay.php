<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocumentExpiryAddScheduleComplianceDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_expiry_color_settings', function (Blueprint $table) {
            $table->unsignedInteger("schedule_grace_period_days")->nullable()->after("overdue_period_font_color_code");
            $table->string("schedule_grace_period_color_code")->default("#000000")->after("schedule_grace_period_days");
            $table->string("schedule_grace_period_font_color_code")->default("#ffffff")->after("schedule_grace_period_color_code");
            $table->unsignedInteger("schedule_alert_period_days")->nullable()->after("schedule_grace_period_font_color_code");
            $table->string("schedule_alert_color_code")->default("#000000")->after("schedule_alert_period_days");
            $table->string("schedule_alert_period_font_color_code")->default("#ffffff")->after("schedule_alert_color_code");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_expiry_color_settings', function (Blueprint $table) {
            $table->dropColumn("schedule_grace_period_days");
            $table->dropColumn("schedule_grace_period_color_code");
            $table->dropColumn("schedule_grace_period_font_color_code");
            $table->dropColumn("schedule_alert_period_days");
            $table->dropColumn("schedule_alert_color_code");
            $table->dropColumn("schedule_alert_period_font_color_code");
        });
    }
}
