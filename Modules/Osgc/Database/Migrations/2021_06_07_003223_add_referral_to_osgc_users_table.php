<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferralToOsgcUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('osgc_users', function (Blueprint $table) {
            $table->integer('referral')->unsigned()->after('indian_status')->comment('id from global constant');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('osgc_users', function (Blueprint $table) {
            $table->dropColumn('referral');
        });
    }
}
