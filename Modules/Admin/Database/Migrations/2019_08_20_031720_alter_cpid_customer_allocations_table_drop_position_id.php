<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpidCustomerAllocationsTableDropPositionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('cpid_customer_allocations', function (Blueprint $table) {
            $table->dropColumn('position_id');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('cpid_customer_allocations', function (Blueprint $table) {
            $table->integer('position_id')->unsigned()->comment('position id from position lookups');
         });
    }
}
