<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateMiscellaneousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_miscellaneouses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->enum('veteran_of_armedforce', ['No', 'Yes']);
            $table->string('service_number', 255)->nullable();
            $table->string('canadian_force', 255)->nullable();
            $table->date('enrollment_date')->nullable();
            $table->date('release_date')->nullable();
            $table->string('item_release_number', 255)->nullable();
            $table->string('rank_on_release', 255)->nullable();
            $table->string('military_occupation', 255)->nullable();
            $table->string('reason_for_release', 255)->nullable();
            $table->enum('dismissed', ['No', 'Yes']);
            $table->text('explanation_dismissed')->nullable();
            $table->enum('limitations', ['No', 'Yes']);
            $table->text('limitation_explain')->nullable();
            $table->enum('criminal_convicted', ['No', 'Yes']);
            $table->string('offence', 255)->nullable();
            $table->date('offence_date')->nullable();
            $table->string('offence_location')->nullable();
            $table->string('disposition_granted')->nullable();
            $table->string('career_interest')->nullable();
            $table->string('other_roles')->nullable();
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
        Schema::dropIfExists('candidate_miscellaneouses');
    }
}
