<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftModuleDropdownOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_module_dropdown_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('option_name', 200);
            $table->integer('shift_module_dropdown_id')->unsigned()->index('shift_module_dropdown_options_shift_module_dropdown_id_foreign');
            $table->integer('order_sequence')->nullable();
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
        Schema::dropIfExists('shift_module_dropdown_options');
    }
}
