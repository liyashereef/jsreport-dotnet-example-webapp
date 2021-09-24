<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSurveyTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_survey_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string("survey_name", 1000);
            $table->boolean("customer_based")->default(true);
            $table->boolean("role_based")->default(true);
            $table->date("start_date");
            $table->date("expiry_date");
            $table->unsignedInteger("created_by");
            $table->boolean("active");
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
        Schema::dropIfExists('employee_survey_templates');
    }
}
