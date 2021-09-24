<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('content_type_id')->unsigned()->comments('ID from content type');   
            $table->string('value',250)->nullable();  
            $table->integer('course_id')->unsigned()->comments('ID from training_courses');
            $table->integer('fast_forward')->default('1')->comments('1-enable, 0-disable');
            $table->string('content_title',250);
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
        Schema::dropIfExists('course_contents');
    }
}
