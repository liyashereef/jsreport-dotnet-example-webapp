<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpCameraConfigurationWidgetFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_camera_configuration_widget_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ip_camera_configuration_tab_detail_id')->unsigned()->comment('id from ip_camera_configuration_tab_detail table');
            $table->longText('field_display_name');
            $table->longText('field_system_name');
            $table->boolean('default_sort')->default(true);
            $table->integer('default_sort_order')->nullable();
            $table->text('permission_text')->nullable();
            $table->boolean('visible')->default(true);
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
        Schema::dropIfExists('ip_camera_configuration_widget_fields');
    }
}
