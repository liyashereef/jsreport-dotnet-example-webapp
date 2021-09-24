<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpidLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpid_lookups', function (Blueprint $table) {

            $table->increments('id');
            $table->string('cpid')->nullable();
            $table->string('short_name', 150)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('noc', 100)->nullable();
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
        Schema::dropIfExists('cpid_lookups');
    }
}
