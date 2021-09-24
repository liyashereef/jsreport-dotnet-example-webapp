<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPmTasksAddCompletionDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('pm_tasks', function (Blueprint $table) {
            $table->dateTime('completed_date')->nullable()->after('due_date');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pm_tasks', function (Blueprint $table) {
            $table->dropColumn('completed_date');
        });
    }
}
