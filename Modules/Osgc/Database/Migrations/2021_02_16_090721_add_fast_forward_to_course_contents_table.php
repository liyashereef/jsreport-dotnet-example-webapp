<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFastForwardToCourseContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('osgc_course_contents', function (Blueprint $table) {
            $table->integer('fast_forward')->default('1')->comments('1-enable, 0-disable')->after('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('osgc_course_contents', function (Blueprint $table) {
            $table->dropColumn('fast_forward');
        });
    }
}
