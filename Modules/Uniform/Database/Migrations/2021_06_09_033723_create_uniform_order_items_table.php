<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniformOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniform_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->double('item_price', 8, 2);
            $table->integer('quantity')->nullable();
            $table->unsignedInteger('uniform_order_id');
            $table->unsignedInteger('uniform_product_id');
            $table->unsignedInteger('uniform_product_variant_id');
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
        Schema::dropIfExists('uniform_order_items');
    }
}
