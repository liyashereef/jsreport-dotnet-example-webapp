<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotifyToUserRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_ratings', function (Blueprint $table) {
            $table->boolean('notify_employee')->default(0)->after('rating');
            $table->integer('policy_id')->nullable()->after('notify_employee');
            $table->string('response',1000)->nullable()->after('policy_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_ratings', function (Blueprint $table) {
            $table->dropColumn('notify_employee');
            $table->dropColumn('policy_id');
            $table->dropColumn('response');
        });
    }
}
