<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRenameCourseMasterIdInCourseQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('osgc_test_course_questions', function (Blueprint $table) {
            $table->renameColumn('osgc_course_master_id', 'question_master_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('osgc_test_course_questions', function (Blueprint $table) {
            $table->renameColumn('question_master_id', 'osgc_course_master_id');
        });
    }
}
