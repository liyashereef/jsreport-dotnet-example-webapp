<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('first_name', 191);
            $table->string('last_name', 191);
            $table->date('dob')->nullable();
            $table->string('email', 191);
            $table->string('phone', 191)->nullable();
            $table->string('phone_cellular', 191)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('postal_code', 191)->nullable();
            $table->string('geo_location_lat', 255)->nullable();
            $table->string('geo_location_long', 255)->nullable();
            $table->integer('smart_phone_type_id')->unsigned()->nullable();
            $table->string('smart_phone_skill_level', 255)->nullable();
            $table->string('profile_image', 191)->nullable();
            $table->string('username', 191);
            $table->string('password', 191);
            $table->dateTime('last_login')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('terms_accepted')->default(0);
            $table->boolean('password_changed')->default(0);
            $table->boolean('is_activated')->default(0);
            $table->boolean('is_completed')->default(0);
            $table->integer('gender')->default(0);
            $table->rememberToken();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidates');
    }
}
