<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateWageExpectationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_wage_expectations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->double('wage_expectations_from', 15,4);
            $table->double('wage_expectations_to', 15,4);
            $table->double('wage_last_hourly', 15,4)->nullable();
            $table->enum('current_paystub', ['Yes', 'No']);
            $table->string('wage_last_provider', 255)->nullable();
            $table->string('wage_last_provider_other', 255)->nullable();
            $table->string('last_role_held', 255)->nullable();
            $table->string('explanation_wage_expectation', 500)->nullable();
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
        Schema::dropIfExists('candidate_wage_expectations');
    }

}
