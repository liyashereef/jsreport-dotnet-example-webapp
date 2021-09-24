<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSiteRateScheduleCustomerRequirementToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE schedule_customer_requirements MODIFY COLUMN site_rate decimal(10,2)  ");
        Schema::table('schedule_customer_requirements', function (Blueprint $table) {
            //$table->decimal('site_rate',10,2)->default(0)->change();
 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_customer_requirements', function (Blueprint $table) {
            $table->float('site_rate',10,2)->default(0)->change();
        });
    }
}
