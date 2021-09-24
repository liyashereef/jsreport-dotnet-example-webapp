<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientFeedbackStatusLookup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_employee_feedbacks', function (Blueprint $table) {
            $table->unsignedInteger('status_lookup_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_employee_feedbacks', function (Blueprint $table) {
            $table->unsignedInteger('status_lookup_id')->nullable(false)->change();

        });
    }
}
