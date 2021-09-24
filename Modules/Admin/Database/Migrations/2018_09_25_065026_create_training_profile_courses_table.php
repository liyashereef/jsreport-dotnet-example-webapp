<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingProfileCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_profile_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('training_profile_id')->unsigned()->comments('ID from training_profiles or training_profile_sites. If profile_type=employee means, the ID pointing to training_profiles table else profile_type=site, then the ID pointing to training_profile_sites');            
            $table->integer('course_id')->unsigned()->comments('ID from courses');            
            $table->string('course_type',50)->default('recommended')->comments('Either mandatory or recommended');            
            $table->string('profile_type',50)->default('employee')->comments('Either employee or site');            
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
        Schema::dropIfExists('training_profile_courses');
    }
}
