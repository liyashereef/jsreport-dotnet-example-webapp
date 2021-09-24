<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestCourseMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_course_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('training_course_id')->unsigned()->comment('id from training course table');
            $table->string('exam_name',255);
            $table->integer('number_of_question')->default(0)->comment('all questions will display');
            $table->boolean('random_question')->default(0);
            $table->integer('pass_percentage');
            $table->boolean('active')->default(0);
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('test_course_masters');
    }
}
