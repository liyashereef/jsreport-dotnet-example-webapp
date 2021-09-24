<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateSecurityClearancesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_security_clearances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->enum('born_outside_of_canada', ['Yes', 'No']);
            $table->enum('work_status_in_canada', ['Landed Immigrant', 'Permanent Resident', 'Canadian Citizen']);
            $table->string('years_lived_in_canada', 500)->nullable();
            $table->enum('prepared_for_security_screening', ['Yes', 'No']);
            $table->enum('no_clearance', ['Yes', 'No']);
            $table->string('no_clearance_explanation', 500)->nullable();
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
        Schema::dropIfExists('candidate_security_clearances');
    }

}
