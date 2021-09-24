<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftModulePostOrdersTable extends Migration
      
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_module_post_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned()->index('shift_module_post_orders_customer_id_foreign');
            $table->integer('module_id')->unsigned()->index('shift_module_post_orders_module_id_foreign');
            $table->integer('shift_id')->unsigned();
            $table->dateTime('shift_start_date')->nullable();
            $table->integer('field_id')->unsigned();
            $table->string('field_value', 200);
            $table->decimal('duration',15,2)->nullable();
            $table->decimal('percentage',15,2)->nullable();
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
        Schema::dropIfExists('shift_module_post_orders');
    }
}
