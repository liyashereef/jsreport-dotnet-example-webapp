<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateSecurityGuardingExperincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_security_guarding_experinces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->enum('guard_licence', ['Yes', 'No']);
            $table->date('start_date_guard_license')->nullable();
            $table->date('start_date_first_aid')->nullable();
            $table->date('start_date_cpr')->nullable();
            $table->date('expiry_guard_license')->nullable();
            $table->date('expiry_first_aid')->nullable();
            $table->date('expiry_cpr')->nullable();
            $table->decimal('test_score_percentage', 15, 2)->nullable();
            $table->string('test_score_document_id', 255)->nullable();
            $table->enum('security_clearance', ['Yes', 'No'])->nullable();
            $table->text('security_clearance_type')->nullable();
            $table->date('security_clearance_expiry_date')->nullable();
            $table->float('years_security_experience')->nullable();
            $table->string('most_senior_position_held', 3)->nullable();
            $table->text('positions_experinces')->nullable();
            $table->string('social_insurance_number')->nullable();
            $table->boolean('sin_expiry_date_status')->nullable();
            $table->date('sin_expiry_date')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_security_guarding_experinces');
    }
}
