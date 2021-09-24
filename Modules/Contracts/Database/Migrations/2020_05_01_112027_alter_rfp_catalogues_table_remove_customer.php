<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRfpCataloguesTableRemoveCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rfp_catalogues', function (Blueprint $table) {
            $table->dropColumn(['customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rfp_catalogues', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('group_id')->nullable();
        });
    }
}
