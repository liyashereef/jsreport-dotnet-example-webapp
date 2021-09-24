<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterUniformOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniform_order_items', function (Blueprint $table) {
            $table->unsignedInteger('tax_id')->after('quantity');
            $table->double('tax_rate', 8, 2)->after('quantity');
            $table->double('tax_amount', 8, 2)->after('quantity');
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
            $table->dropColumn('tax_id');
            $table->dropColumn('tax_rate');
            $table->dropColumn('tax_amount');
        });
    }
}
