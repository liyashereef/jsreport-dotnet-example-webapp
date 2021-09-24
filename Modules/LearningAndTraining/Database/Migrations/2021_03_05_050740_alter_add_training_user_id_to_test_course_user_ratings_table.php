<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddTrainingUserIdToTestCourseUserRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_course_user_ratings', function (Blueprint $table) {
            $table->integer('training_user_id')->nullable()->after('rating');
            $table->integer('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_course_user_ratings', function (Blueprint $table) {
            $table->dropColumn('training_user_id');
            $table->integer('user_id')->nullable(false)->change();
        });
    }
}
