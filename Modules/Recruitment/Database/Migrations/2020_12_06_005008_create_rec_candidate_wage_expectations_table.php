<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateWageExpectationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_wage_expectations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->double('wage_expectations', 15, 4);
            //$table->double('wage_expectations_to', 15, 4);
            $table->double('wage_last_hourly', 15, 4)->nullable();
            $table->enum('current_paystub', ['Yes', 'No']);
            $table->string('wage_last_provider', 255)->nullable();
            $table->string('wage_last_provider_other', 255)->nullable();
            $table->string('last_role_held', 255)->nullable();
            $table->string('explanation_wage_expectation', 500)->nullable();
            $table->text('security_provider_strengths')->nullable();
            $table->text('security_provider_notes')->nullable();
            $table->integer('rate_experience')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_wage_expectations');
    }
}
