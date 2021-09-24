<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterOrderItemsTablesTable extends Migration
{
    /**
     * Run the migrations.  
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniform_order_items', function (Blueprint $table) {
            $table->unsignedInteger('uniform_product_variant_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniform_order_items', function (Blueprint $table) {
            $table->unsignedInteger('uniform_product_variant_id')->nullable(false)->change();
        });
    }
}
