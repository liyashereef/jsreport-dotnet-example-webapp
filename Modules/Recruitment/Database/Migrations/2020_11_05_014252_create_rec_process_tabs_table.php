<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecProcessTabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_process_tabs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('system_name', 255);
            $table->string('display_name', 255);
            $table->integer('order')->nullable();
            $table->text('instructions')->nullable();
            $table->text('detailed_help')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_process_tabs');
    }
}
