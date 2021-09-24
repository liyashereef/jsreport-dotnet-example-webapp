<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCompetencyMatrixLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_competency_matrix_lookups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('competency_matrix_category_id')->nullable();
            $table->string('competency')->nullable();
            $table->text('definition')->nullable();
            $table->text('behavior')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->dropIfExists('rec_competency_matrix_lookups');
    }
}
