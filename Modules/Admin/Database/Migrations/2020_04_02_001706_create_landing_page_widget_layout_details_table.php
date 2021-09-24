<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandingPageWidgetLayoutDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_page_widget_layout_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('landing_page_widget_layout_id')->unsigned()->comment('id from landing_page_widget_layouts table');
            $table->integer('row_index');
            $table->integer('column_index');
            $table->integer('rowspan');
            $table->integer('colspan');
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
        Schema::dropIfExists('landing_page_widget_layout_details');
    }
}
