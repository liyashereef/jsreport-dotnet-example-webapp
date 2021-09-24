<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSurveyRoleAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_survey_role_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("role_id");
            $table->unsignedInteger("survey_id");
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
        Schema::dropIfExists('employee_survey_role_allocations');
    }
}
