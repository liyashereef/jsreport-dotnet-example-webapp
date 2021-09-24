<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateSecurityGuardingExperincesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_security_guarding_experinces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->enum('guard_licence', ['Yes', 'No']);
            $table->date('start_date_guard_license')->nullable();
            $table->date('start_date_first_aid')->nullable();
            $table->date('start_date_cpr')->nullable();
            $table->date('expiry_guard_license')->nullable();
            $table->date('expiry_first_aid')->nullable();
            $table->date('expiry_cpr')->nullable();
            $table->float('years_security_experience')->nullable();
            $table->string('most_senior_position_held', 3)->nullable();
            $table->text('positions_experinces')->nullable();
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
        Schema::dropIfExists('candidate_security_guarding_experinces');
    }

}
