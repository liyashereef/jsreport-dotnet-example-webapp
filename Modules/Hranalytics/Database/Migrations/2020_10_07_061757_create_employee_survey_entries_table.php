<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSurveyEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_survey_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("survey_id");
            $table->unsignedInteger("customer_id");
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("created_by");
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
        Schema::dropIfExists('employee_survey_entries');
    }
}
