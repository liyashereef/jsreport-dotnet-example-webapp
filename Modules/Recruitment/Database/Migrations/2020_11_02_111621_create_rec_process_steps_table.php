<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecProcessStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_process_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('step_order');
            $table->string('step_name', 255);
            $table->string('display_name', 255)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('type')->default(0)->comment('0-Manual/1-Automated');
            $table->string('route', 255)->nullable();
            $table->integer('tab_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->dropIfExists('rec_process_steps');
    }
}
