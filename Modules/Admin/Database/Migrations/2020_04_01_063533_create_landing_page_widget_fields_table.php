<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandingPageWidgetFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_page_widget_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('landing_page_tab_detail_id')->unsigned()->comment('id from landing_page_tab_details table');
            $table->string('field_display_name', 40);
            $table->text('field_system_name', 40);
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
        Schema::dropIfExists('landing_page_widget_fields');
    }
}
