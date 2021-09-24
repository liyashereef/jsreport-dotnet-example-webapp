<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateSettingRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_setting_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_setting_id')->unsigned()->index('template_setting_rules_template_setting_id_foreign');
            $table->integer('color_id')->unsigned()->index('template_setting_rules_color_id_foreign');
            $table->float('min_value', 8, 4);
            $table->float('max_value', 8, 4);
            $table->timestamps();
            $table->softDeletes(); 
        });

        Schema::table('template_setting_rules', function (Blueprint $table) {
            $table->foreign('template_setting_id')->references('id')->on('template_settings')->onUpdate('RESTRICT')->onDelete('RESTRICT');            
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_setting_rules', function(Blueprint $table)
        {
                $table->dropForeign('template_setting_rules_template_setting_id_foreign');
        });             
        Schema::dropIfExists('template_setting_rules');
    }
}
