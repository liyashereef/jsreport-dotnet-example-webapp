<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_code',255)->nullable()->comments('Reference Code');
            $table->integer('training_category_id')->unsigned()->comments('ID from training_categories');            
            $table->string('course_title',250);            
            $table->string('course_description',1000);
            $table->string('course_objectives',1000);
            $table->string('course_file',255)->nullable();
            $table->string('course_external_url',255)->nullable();
//            $table->date('course_due_date')->nullable();
            $table->integer('status')->default('1')->comments('1-Active, 0-Inactive');
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
        Schema::dropIfExists('training_courses');
    }
}
