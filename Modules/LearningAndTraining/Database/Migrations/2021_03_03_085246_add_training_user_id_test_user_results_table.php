<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrainingUserIdTestUserResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_user_results', function (Blueprint $table) {
             $table->integer('training_user_id')->nullable()->after('submitted_at');
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
        Schema::table('test_user_results', function (Blueprint $table) {
            $table->dropColumn('training_user_id');
            $table->integer('user_id')->nullable(false)->change();
        });
    }
}
