<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPmTasksTableAddFollowersCanUpdate extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_tasks', function (Blueprint $table) {
            $table->boolean('followers_can_update')->comment('0-no, 1-yes')->default(0);
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
            $table->dropColumn('followers_can_update');
        });
    }

}
