<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingUserCourseAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_user_course_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('course_id');
            $table->boolean('mandatory')->default(0);
            $table->boolean('recommended')->default(0);
            $table->boolean('completed')->default(0);
            $table->string('completed_percentage')->default(0);
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
        Schema::dropIfExists('training_user_course_allocations');
    }
}
