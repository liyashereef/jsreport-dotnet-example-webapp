<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocumentExpiryAddFontColor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_expiry_color_settings', function (Blueprint $table) {
            $table->string("grace_period_font_color_code")->default("#000000")->after("grace_period_color_code");
            $table->string("alert_period_font_color_code")->default("#ffffff")->after("alert_period_color_code");
            $table->string("overdue_period_color_code")->default("#000000")->after("alert_period_font_color_code");
            $table->string("overdue_period_font_color_code")->default("#ffffff")->after("overdue_period_color_code");
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
            $table->dropColumn("grace_period_font_color_code");
            $table->dropColumn("alert_period_font_color_code");
            $table->dropColumn("overdue_period_color_code");
            $table->dropColumn("overdue_period_font_color_code");
        });
    }
}
