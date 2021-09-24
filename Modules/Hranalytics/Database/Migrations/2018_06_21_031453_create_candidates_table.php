<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidatesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('candidates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('phone_home', 13)->nullable();
            $table->string('phone_cellular', 13)->nullable();
            $table->string('address', 255);
            $table->string('city', 255);
            $table->string('postal_code', 6);
            $table->string('geo_location_lat',255)->nullable();
            $table->string('geo_location_long',255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('candidates');
    }

}
