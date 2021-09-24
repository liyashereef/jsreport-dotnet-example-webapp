<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateTransitionAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_transition_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_transition_id')->unsigned()->index('candidate_transition_attachments_candidate_transition_id_foreign');
            $table->integer('attachment_id')->unsigned()->index('candidate_transition_attachments_attachment_id_foreign');
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
        Schema::dropIfExists('candidate_transition_attachments');
    }
}
