<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsgcTestCourseMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osgc_test_course_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned()->comment('id from osgc course table');
            $table->integer('osgc_course_section_id')->unsigned()->comment('id from osgc course section table');
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
        Schema::dropIfExists('osgc_test_course_masters');
    }
}
