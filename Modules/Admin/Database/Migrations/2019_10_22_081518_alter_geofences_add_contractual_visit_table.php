<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGeofencesAddContractualVisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geo_fences', function (Blueprint $table) {
            $table->integer('contractual_visit')->after('active')->nullable();
            $table->integer('unit')->after('contractual_visit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geo_fences', function (Blueprint $table) {
            $table->dropColumn('contractual_visit');
            $table->dropColumn('unit');
        });
    }
}
