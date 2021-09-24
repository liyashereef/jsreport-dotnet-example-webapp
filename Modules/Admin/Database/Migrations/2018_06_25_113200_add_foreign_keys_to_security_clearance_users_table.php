<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToSecurityClearanceUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('security_clearance_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('security_clearance_lookup_id')->references('id')->on('security_clearance_lookups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('security_clearance_users', function (Blueprint $table) {            
            $table->dropForeign('security_clearance_users_security_clearance_lookup_id_foreign');
            $table->dropForeign('security_clearance_users_user_id_foreign');
        });
    }
}
