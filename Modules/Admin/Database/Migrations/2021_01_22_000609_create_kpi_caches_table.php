<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKpiCachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_caches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->comment('md5 hash of input queries');
            $table->text('value')->comment('Last processed json response');
            $table->integer('hit')->default(0)->comment('Cache hit count');
            $table->string('query')->nullable()->comment('Input queries');
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
        Schema::dropIfExists('kpi_caches');
    }
}
