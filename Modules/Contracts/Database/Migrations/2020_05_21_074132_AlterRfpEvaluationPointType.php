<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRfpEvaluationPointType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('rfp_evaluation_criterias', function (Blueprint $table) {
            $table->decimal('points',13,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rfp_evaluation_criterias', function (Blueprint $table) {
            $table->integer('points',10)->change();
        });
    }
}
