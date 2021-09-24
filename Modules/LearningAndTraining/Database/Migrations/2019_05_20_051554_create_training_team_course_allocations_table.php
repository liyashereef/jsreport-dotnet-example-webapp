<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingTeamCourseAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_team_course_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id');
            $table->integer('team_id');
            $table->integer('parent_team_id')->default(0);
            $table->boolean('mandatory')->nullable();
            $table->boolean('recommended')->nullable();
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
        Schema::dropIfExists('training_team_course_allocations');
    }
}
