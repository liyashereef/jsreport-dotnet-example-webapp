<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientEmployeeFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_employee_feedbacks', function ($table) {
            $table->unsignedInteger('status_lookup_id')->after('customer_id');
            $table->string('reg_manager_notes', 1000)->nullable()->after('status_lookup_id');
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
            $table->dropColumn('status_lookup_id');
            $table->dropColumn('reg_manager_notes');
        });

    }
}
