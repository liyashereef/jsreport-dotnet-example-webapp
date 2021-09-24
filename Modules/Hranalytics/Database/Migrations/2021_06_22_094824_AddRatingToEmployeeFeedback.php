<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRatingToEmployeeFeedback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_feedback', function (Blueprint $table) {
            $table->unsignedInteger("rating_id")->after("department_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_feedback', function (Blueprint $table) {
            $table->dropColumn("rating_id");
        });
    }
}
