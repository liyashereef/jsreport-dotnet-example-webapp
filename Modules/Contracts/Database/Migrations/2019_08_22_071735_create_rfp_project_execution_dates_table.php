<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfpProjectExecutionDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfp_project_execution_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rfp_details_id')->nullable();
            $table->string('project_execution_other_date_label')->nullable();
            $table->date('project_execution_other_date_value')->nullable();
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
        Schema::dropIfExists('rfp_project_execution_dates');
    }
}
