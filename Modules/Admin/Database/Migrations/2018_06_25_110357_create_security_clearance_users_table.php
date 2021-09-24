<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSecurityClearanceUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('security_clearance_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('security_clearance_users_user_id_foreign');
            $table->integer('security_clearance_lookup_id')->unsigned()->index('security_clearance_users_security_clearance_lookup_id_foreign');
            $table->string('value', 191)->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('security_clearance_users');
    }
}
