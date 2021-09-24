<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateAvailabilitiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_availabilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->enum('current_availability', ['Full-Time (Around 40 hours per week)', 'Part-Time (Less than 40 hours per week)']);
            $table->text('days_required')->nullable();
            $table->text('shifts')->nullable();
            $table->text('availability_explanation')->nullable();
            $table->date('availability_start');
            $table->enum('understand_shift_availability', ['Yes', 'No']);
            $table->enum('available_shift_work', ['Yes', 'No']);
            $table->string('explanation_restrictions', 500)->nullable();
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
        Schema::dropIfExists('candidate_availabilities');
    }

}
