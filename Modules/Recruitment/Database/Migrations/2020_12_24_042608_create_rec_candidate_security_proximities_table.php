<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateSecurityProximitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_security_proximities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->enum('driver_license', ['I have a valid G1 license', 'I have a valid G2 license', 'I have a full class G license'])->nullable();
            $table->enum('access_vehicle', ['I do not have access to a vehicle', 'I have access to a vehicle that is not my own', 'I have my own vehicle']);
            $table->enum('access_public_transport', ['I have little access to the client site via public transit', 'I have some access to the client site via public transit', 'I have ready access to the client site via public transit'])->nullable();
            $table->enum('transportation_limitted', ['Yes', 'No'])->nullable();
            $table->string('explanation_transport_limit', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_security_proximities');
    }
}
