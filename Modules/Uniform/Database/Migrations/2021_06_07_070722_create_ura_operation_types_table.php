<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUraOperationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ura_operation_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->string('machine_name');
            $table->boolean('restricted')->comment('0:visible for frontend');

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
        Schema::dropIfExists('ura_operation_types');
    }
}
