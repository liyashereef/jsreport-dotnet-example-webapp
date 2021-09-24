<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniformSchedulingCustomQuestionOptionAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniform_scheduling_custom_question_option_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uniform_scheduling_custom_question_id')->unsigned();
            $table->integer('uniform_scheduling_custom_option_id')->unsigned();
            $table->integer('option_sort_order')->nullable();
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
        Schema::dropIfExists('uniform_scheduling_custom_question_option_allocations');
    }
}
