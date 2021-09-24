<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruitingAnalyticsTabDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruiting_analytics_tab_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('recruiting_analytics_tab_id')->unsigned()->comment('id from recruiting_analytics_tabs table');
            $table->integer('landing_page_widget_layout_detail_id')->unsigned()->comment('id from landing_page_widget_layout_detail table');
            $table->integer('landing_page_module_widget_id')->unsigned()->comment('id from landing_page_module_widgets table');
            $table->text('landing_page_module_widget_type');
            $table->integer('created_by')->unsigned()->comment('id from users table');
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
        Schema::dropIfExists('recruiting_analytics_tab_details');
    }
}
