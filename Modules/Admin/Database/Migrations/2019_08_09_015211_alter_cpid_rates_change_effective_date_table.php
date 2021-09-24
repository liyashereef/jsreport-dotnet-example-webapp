<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpidRatesChangeEffectiveDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cpid_rates', function(Blueprint $table)
        {
            $table->renameColumn('effective_date', 'effective_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cpid_rates', function(Blueprint $table)
        {
            $table->renameColumn('effective_from', 'effective_date');
        });
    }
}
