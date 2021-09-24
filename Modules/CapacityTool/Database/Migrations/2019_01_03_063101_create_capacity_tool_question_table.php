<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapacityToolQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capacity_tool_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question')->nullable();
            $table->string('answer_type')->nullable();
            $table->boolean('is_parent')->nullable()->comment('Whether parent exists');
            $table->integer('parent_id')->nullable()->comment('Parent question id');
            $table->string('show_child_value')->nullable()->comment('Parent answer value if the question to be shown');
            $table->string('tooltip', 500)->nullable();
            $table->string('field_type')->nullable()->comment('Field type of answer field');
            $table->integer('order')->nullable()->comment('Order of the quetion');
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
        Schema::dropIfExists('capacity_tool_questions');
    }
}
