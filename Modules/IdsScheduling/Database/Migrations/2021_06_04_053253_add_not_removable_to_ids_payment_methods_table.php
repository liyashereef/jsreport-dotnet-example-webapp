<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotRemovableToIdsPaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_payment_methods', function (Blueprint $table) {
            $table->boolean('not_removable')->default(0)->after('active')->comment("0-removable, 1-not removable");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ids_payment_methods', function (Blueprint $table) {
            $table->dropColumn('not_removable');
        });
    }
}
