<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('osgc_courses', function (Blueprint $table) {
            $table->text('description')->after('title')->nullable();
            $table->string('course_image')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('osgc_courses', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('course_image');
        });
    }
}
