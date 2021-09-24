<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterUniformOrderPayAmtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('uniform_orders', 'payable_amount'))
        {
            Schema::table('uniform_orders', function (Blueprint $table) {
                $table->dropColumn('payable_amount');
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniform_orders', function (Blueprint $table) {
            $table->double('payable_amount', 8, 2)->default(0.00)->after('price');
        });
        
    }
}
