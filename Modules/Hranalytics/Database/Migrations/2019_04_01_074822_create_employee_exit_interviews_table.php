<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeExitInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_exit_interviews', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('unique_id',255)->nullable();
            $table->integer('project_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('exit_interview_reason_id')->unsigned()->comment('1=resignation and 2 = termination');
            $table->integer('exit_interview_reason_details')->unsigned();
            $table->string('exit_interview_explanation', 2000)->nullable();
            $table->integer('created_by')->unsigned();
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
        Schema::dropIfExists('employee_exit_interviews');
    }
}
