<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCourseSectionIdToAllocatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('osgc_allocated_user_courses', function (Blueprint $table) {
            $table->integer('course_section_id')->unsigned()->after('course_id')->comment('id from osgc course content section table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('osgc_allocated_user_courses', function (Blueprint $table) {
            $table->dropColumn('course_section_id');
        });
    }
}
