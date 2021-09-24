<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsgcUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osgc_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned()->default(0)->comment('id from osgc courses table');
            $table->integer('course_section_id')->unsigned()->default(0)->comment('id from osgc course content section table');
            $table->string('first_name', 191);
            $table->string('last_name', 191)->nullable();
            $table->string('email', 191);   
            $table->string('password', 191);
            $table->boolean('is_veteran')->default(0);
            $table->boolean('indian_status')->default(0);
            $table->dateTime('last_login')->nullable();
            $table->boolean('active')->default(0);
            $table->string('verification_token');
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
        Schema::dropIfExists('osgc_users');
    }
}
