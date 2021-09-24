<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 191);
            $table->string('last_name', 191)->nullable();
            $table->string('username', 191);
            $table->string('password', 191);
            $table->string('email', 191)->nullable();            
            $table->string('alternate_email', 191)->nullable();
            $table->string('phoneno', 191);
            $table->unsignedInteger('customer_id');
            $table->boolean('internaluser')->default(true);
            $table->dateTime('last_login')->nullable();
            $table->boolean('active')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('facility_users');
    }
}
