<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpCameraConfigurationTabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_camera_configuration_tabs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('tab_name');
            $table->integer('landing_page_widget_layout_id')->unsigned()->comment('id from landing_page_widget_layouts table');
            $table->integer('seq_no');
            $table->boolean('default_tab_structure')->default(true);
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('ip_camera_configuration_tabs');
    }
}
