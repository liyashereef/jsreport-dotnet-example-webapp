<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPmTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('pm_tasks', function (Blueprint $table) {
            $table->integer('deadline_weightage')->unsigned()->nullable()->after('efficiency_rating_id');
            $table->integer('value_add_weightage')->unsigned()->nullable()->after('deadline_weightage');
            $table->integer('initiative_weightage')->unsigned()->nullable()->after('value_add_weightage');
            $table->integer('commitment_weightage')->unsigned()->nullable()->after('initiative_weightage');
            $table->integer('complexity_weightage')->unsigned()->nullable()->after('commitment_weightage');
            $table->integer('efficiency_weightage')->unsigned()->nullable()->after('complexity_weightage');
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
            $table->dropColumn('deadline_weightage');
            $table->dropColumn('value_add_weightage');
            $table->dropColumn('initiative_weightage');
            $table->dropColumn('commitment_weightage');
            $table->dropColumn('complexity_weightage');
            $table->dropColumn('efficiency_weightage');
        });
    }
}
