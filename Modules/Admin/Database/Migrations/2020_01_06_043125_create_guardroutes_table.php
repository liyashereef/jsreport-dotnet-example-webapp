<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuardroutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guardroutes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('routename');
            $table->string('description')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('createdby');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('createdby')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guardroutes');
    }
}
