<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfpResponseSubmissionDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfp_response_submission_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rfp_details_id')->nullable();
            $table->string('response_submission_other_date_label')->nullable();
            $table->date('response_submission_other_date_value')->nullable();
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
        Schema::dropIfExists('rfp_response_submission_dates');
    }
}
