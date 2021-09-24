<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTrainingUserTeamsAddTrainingUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_user_teams', function (Blueprint $table) {
            $table->integer('training_user_id')->nullable()->after('team_id');
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
         Schema::table('training_user_teams', function (Blueprint $table) {
            $table->dropColumn('training_user_id');
            $table->integer('user_id')->nullable(false)->change();
         });
    }
}
