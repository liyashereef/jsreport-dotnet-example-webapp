<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStcReportingTemplateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stc_reporting_template_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('color_id')->unsigned()->index('stc_reporting_template_rules_color_id_foreign');
            $table->float('min_value', 8, 5);
            $table->float('max_value', 8, 5);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('stc_reporting_template_rules', function (Blueprint $table) {
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
        Schema::table('stc_reporting_template_rules', function (Blueprint $table) {
            $table->dropForeign('stc_reporting_template_rules_color_id_foreign');
        });
        Schema::dropIfExists('stc_reporting_template_rules');
    }
}
