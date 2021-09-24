<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpCameraConfigurationTabDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_camera_configuration_tab_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ip_camera_configuration_tab_id')->unsigned()->comment('id from ip_camera_configuration_tab table');
            $table->integer('landing_page_widget_layout_detail_id')->unsigned()->comment('id from landing_page_widget_layout_detail table');
            $table->integer('ip_camera_id')->unsigned()->comment('id from ip camera table');
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
        Schema::dropIfExists('ip_camera_configuration_tab_details');
    }
}
