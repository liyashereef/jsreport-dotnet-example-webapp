<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateClientEmployeeFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_employee_feedbacks', function ($table) {
            $table->integer('feedback_id')->after('customer_id');
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
        Schema::table('client_employee_feedbacks', function ($table) {
            $table->dropColumn('feedback_id');
            $table->integer('user_id')->change();
        });
    }
}
