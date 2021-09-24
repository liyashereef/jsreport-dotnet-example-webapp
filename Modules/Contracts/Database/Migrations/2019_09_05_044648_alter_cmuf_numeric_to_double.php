<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCmufNumericToDouble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmufs', function (Blueprint $table) {
            $table->decimal('contract_length',15,2)->change();
            $table->decimal('total_hours_perweek',15,2)->change();
            $table->decimal('contract_length_renewal_years',15,2)->change();
            
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
            $table->unsignedInteger('contract_length')->change();
            $table->unsignedInteger('total_hours_perweek')->change();
            $table->unsignedInteger('contract_length_renewal_years')->change();
            
        });
    }
}
