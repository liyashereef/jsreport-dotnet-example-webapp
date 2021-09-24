<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameQuestionIdInOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('osgc_test_course_question_options', function (Blueprint $table) {
            $table->renameColumn('osgc_course_question_id', 'question_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('osgc_test_course_question_options', function (Blueprint $table) {
            $table->renameColumn('question_id', 'osgc_course_question_id');
        });
    }
}
