<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPmTasksTableAddUniqueKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('pm_tasks', function (Blueprint $table) {
          $table->string('unique_key')->after('name');
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
            $table->dropColumn('unique_key');
        });
    }
}
