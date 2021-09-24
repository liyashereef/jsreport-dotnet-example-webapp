<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPmTasksAssigneeToNullable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_tasks', function (Blueprint $table) {
            $table->integer('assigned_to')->unsigned()->nullable()->change();
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
            $table->integer('assigned_to')->unsigned()->change();
        });
    }

}
