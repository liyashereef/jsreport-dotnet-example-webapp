<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeOffLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_off_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('time_off_id')->comment('id from leave table');
            $table->boolean('approved')->nullable()->comment('decision');
            $table->text('notes')->nullable()->comment('notes by user');
            $table->integer('days_approved')->nullable();
            $table->integer('days_rejected')->nullable();
            $table->integer('days_remaining')->nullable();  
            $table->date('start_date');
            $table->date('end_date');            
            $table->integer('created_by')->comment('current login');
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
        Schema::dropIfExists('time_off_log');
    }
}
