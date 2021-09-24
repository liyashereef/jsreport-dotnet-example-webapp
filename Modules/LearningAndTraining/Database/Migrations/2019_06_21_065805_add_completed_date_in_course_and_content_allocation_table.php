<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompletedDateInCourseAndContentAllocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_user_course_allocations', function (Blueprint $table) {
            $table->date('completed_date')->nullable()->after('completed');
        });

        Schema::table('training_user_contents', function (Blueprint $table) {
            $table->date('completed_date')->nullable()->after('completed');
        });
        Schema::table('course_contents', function (Blueprint $table) {
            $table->date('content_order')->nullable()->after('content_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_user_course_allocations', function (Blueprint $table) {
            $table->dropColumn('completed_date');
        });

        Schema::table('training_user_contents', function (Blueprint $table) {
            $table->dropColumn('completed_date');
        });
        Schema::table('course_contents', function (Blueprint $table) {
            $table->dropColumn('content_order');
        });
    }
}
