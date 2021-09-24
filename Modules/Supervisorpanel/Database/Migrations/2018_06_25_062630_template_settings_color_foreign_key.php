<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TemplateSettingsColorForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_settings', function (Blueprint $table) {
            $table->foreign('color_id')->references('id')->on('colors')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_settings', function(Blueprint $table)
        {
                $table->dropForeign('template_settings_color_id_foreign');
        });    
    }
}
