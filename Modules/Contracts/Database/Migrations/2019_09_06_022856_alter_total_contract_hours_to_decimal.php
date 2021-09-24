<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTotalContractHoursToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmufs', function (Blueprint $table) {
            $table->decimal('total_hours_perweek',13,2)->nullable()->change();   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cmufs', function (Blueprint $table) {
            $table->unsignedinteger('total_hours_perweek')->default("0")->change(); 
        });
    }
}
