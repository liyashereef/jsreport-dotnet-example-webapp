<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterUniformOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniform_order_items', function (Blueprint $table) {
            $table->double('unit_price', 8, 2)->after('quantity');
            $table->double('total_price_with_tax', 8, 2)->after('quantity');
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
            $table->dropColumn('unit_price');
            $table->dropColumn('total_price_with_tax');
        });
    }
}
