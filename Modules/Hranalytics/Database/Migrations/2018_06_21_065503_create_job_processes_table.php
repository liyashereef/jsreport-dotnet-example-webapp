<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobProcessesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('job_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comments('record creator');
            $table->integer('job_id')->unsigned();
            $table->integer('process_id')->unsigned();
            $table->integer('entered_by_id')->unsigned();
            $table->date('process_date');
            $table->text('process_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('job_processes');
    }

}
