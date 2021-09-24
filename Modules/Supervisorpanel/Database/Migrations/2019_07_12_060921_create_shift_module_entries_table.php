<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftModuleEntriesTable extends Migration
      
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_module_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned()->index('shift_module_entries_customer_id_foreign');
            $table->integer('module_id')->unsigned()->index('shift_module_entries_module_id_foreign');
            $table->integer('shift_id')->unsigned();
            $table->integer('field_id')->unsigned();
            $table->string('field_value', 200);
            $table->integer('attachment_id')->unsigned()->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('shift_module_entries');
    }
}
