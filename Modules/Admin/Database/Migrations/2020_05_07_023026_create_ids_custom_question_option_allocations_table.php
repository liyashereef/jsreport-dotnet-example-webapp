<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsCustomQuestionOptionAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_custom_question_option_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ids_custom_questions_id')->unsigned();
            $table->integer('ids_custom_option_id')->unsigned();
            $table->integer('Ids_option_sort_order')->nullable();
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
        Schema::dropIfExists('ids_custom_question_option_allocations');
    }
}
