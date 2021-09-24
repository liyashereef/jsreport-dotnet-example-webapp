<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCompetencyMatrixRatingLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competency_matrix_rating_lookups', function (Blueprint $table) {
            $table->integer('order_sequence')->nullable()->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competency_matrix_rating_lookups', function (Blueprint $table) {
            $table->dropColumn('order_sequence');
        });
    }
}
