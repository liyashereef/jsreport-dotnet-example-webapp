<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOnboardingStepLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_onboarding_step_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('step_id')->unsigned();
            $table->string('step',250);
            $table->integer('onboarding_id')->unsigned();
            $table->integer('section_id')->unsigned();
            $table->string('section',250);
            $table->date('target_date');
            $table->integer('assigned_to')->unsigned();
            $table->decimal('step_percentage_completed',5,2)->default(0);
            $table->integer('created_by')->unsigned();
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
        Schema::dropIfExists('client_onboarding_step_logs');
    }
}
