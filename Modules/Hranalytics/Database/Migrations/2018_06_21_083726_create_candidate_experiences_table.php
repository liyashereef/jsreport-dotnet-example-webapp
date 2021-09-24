<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_experiences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->enum('current_employee_commissionaries', ['Yes', 'No'])->nullable();
            $table->string('employee_number', 255)->nullable();
            $table->string('currently_posted_site', 255)->nullable();
            $table->enum('position', ['Guard', 'Supervisor'])->nullable();
            $table->string('hours_per_week', 255)->nullable();
            $table->enum('applied_employment', ['Yes', 'No'])->nullable();
            $table->string('position_applied', 255)->nullable();
            $table->date('start_date_position_applied')->nullable();
            $table->date('end_date_position_applied')->nullable();
            $table->enum('employed_by_corps', ['Yes', 'No'])->nullable();
            $table->string('position_employed', 255)->nullable();
            $table->date('start_date_employed')->nullable();
            $table->date('end_date_employed')->nullable();
            $table->integer('location_employed')->nullable()->unsigned();
            $table->string('employee_num', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_experiences');
    }
}
