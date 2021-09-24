<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDurationInTrainingCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_courses', function (Blueprint $table) {
            $table->integer("course_duration")->nullable()->comment('In Minutes')->after("course_image");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_courses', function (Blueprint $table) {
            $table->dropColumn("course_duration");
        });
    }
}
