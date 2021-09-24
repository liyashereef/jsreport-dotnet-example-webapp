<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTrainingUserCourseAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_user_course_allocations', function (Blueprint $table) {
            $table->boolean('manual_completion')->default(0)->after('completed_percentage');
            $table->date('manual_completed_date')->nullable()->after('manual_completion');
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
           $table->dropColumn('manual_completion');
           $table->dropColumn('manual_completed_date');
       });
    }
}
