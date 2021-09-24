<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecUniformItemSizeMeasurementMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_uniform_item_size_measurement_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_name_id')->unsigned();
            $table->integer('size_name_id')->unsigned();
            $table->integer('measurement_name_id')->unsigned();
            $table->integer('min')->unsigned();
            $table->integer('max')->unsigned();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_uniform_item_size_measurement_mappings');
    }
}
