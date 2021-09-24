<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMileageClaimWithRangeKmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE mileage_claims MODIFY COLUMN starting_km float(11,2),
        MODIFY COLUMN ending_km float(11,2),MODIFY COLUMN total_km float(11,2)  ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mileage_claims', function (Blueprint $table) {
            $table->float('starting_km')->change();
            $table->float('ending_km')->change();
             $table->float('total_km')->change();
        });
    }
}
