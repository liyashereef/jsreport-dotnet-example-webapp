<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKpiMasterThresholdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_master_thresholds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kpi_master_allocation_id')->unsigned();
            $table->integer('kpi_threshold_color_id')->unsigned();
            $table->integer('min');
            $table->integer('max');
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('kpi_master_thresholds');
    }
}
