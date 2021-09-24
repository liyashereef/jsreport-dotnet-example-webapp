<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapacityToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capacity_tools', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->nullable();
            $table->integer('capacity_tool_entry_id');
            $table->integer('question_id')->nullable();
            $table->text('answer')->nullable();
            $table->string('answer_type')->nullable();
            //$table->nullableMorphs('answerable');//Adds nullable versions of morphs() columns.
            $table->integer('rating_status_id')->nullable()->comment('Rating status from id');
            $table->text('comment')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('capacity_tools');
    }
}
