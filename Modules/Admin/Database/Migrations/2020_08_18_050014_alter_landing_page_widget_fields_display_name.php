<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLandingPageWidgetFieldsDisplayName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landing_page_widget_fields', function (Blueprint $table) {
            $table->string('field_display_name')->change();
            $table->string('field_system_name')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_page_widget_fields', function (Blueprint $table) {
            $table->string('field_display_name', 40)->change();
            $table->string('field_system_name', 40)->change();
        });
    }
}
