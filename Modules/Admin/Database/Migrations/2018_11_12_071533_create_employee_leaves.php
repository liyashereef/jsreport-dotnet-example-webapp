<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeLeaves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned()->comments('ID from employees')->nullable();
            $table->date('date')->nullable();
            $table->integer('hours_off')->nullable();
            $table->integer('reason_id')->unsigned()->comments('ID from leave reasons')->nullable();
            $table->text('notes', 1000)->nullable();
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
        Schema::dropIfExists('employee_leaves');
    }
}
