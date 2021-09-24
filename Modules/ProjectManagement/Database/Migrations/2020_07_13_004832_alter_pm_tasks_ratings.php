<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPmTasksRatings extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pm_tasks', function (Blueprint $table) {
            $table->dropColumn('rating_id');
            $table->integer('deadline_rating_id')->unsigned()->nullable()->after('rating_id');
            $table->integer('value_add_rating_id')->unsigned()->nullable()->after('deadline_rating_id');
            $table->integer('initiative_rating_id')->unsigned()->nullable()->after('value_add_rating_id');
            $table->integer('commitment_rating_id')->unsigned()->nullable()->after('initiative_rating_id');
            $table->integer('complexity_rating_id')->unsigned()->nullable()->after('commitment_rating_id');
            $table->integer('efficiency_rating_id')->unsigned()->nullable()->after('complexity_rating_id');
            $table->text('rating_notes')->nullable()->after('efficiency_rating_id');
            $table->float('average_rating',8,2)->nullable()->after('rating_notes');
            $table->timestamp('rated_at')->nullable()->after('average_rating');
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
            $table->integer('rating_id'); 
            $table->dropColumn('average_rating');
            $table->dropColumn('deadline_rating_id');
            $table->dropColumn('value_add_rating_id');
            $table->dropColumn('initiative_rating_id');
            $table->dropColumn('commitment_rating_id');
            $table->dropColumn('complexity_rating_id'); 
            $table->dropColumn('efficiency_rating_id');
            $table->dropColumn('rating_notes');
            $table->dropColumn('rated_at');
           
        });
    }
}
