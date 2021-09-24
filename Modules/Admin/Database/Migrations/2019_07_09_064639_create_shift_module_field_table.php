<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftModuleFieldTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_module_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_id')->unsigned()->index('shift_module_field_id_foreign');
            $table->string('field_name', 100);
            $table->integer('field_type')->unsigned();
            $table->boolean('field_status')->default(1);
            $table->integer('dropdown_id')->unsigned()->nullable();
            $table->boolean('is_multiple_photo')->default(1);
            $table->integer('order_id')->unsigned()->nullable();
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
        Schema::dropIfExists('shift_module_fields');
    }

}
