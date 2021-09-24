<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetencyMatrixLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competency_matrix_lookups', function (Blueprint $table) {
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
        Schema::dropIfExists('competency_matrix_lookups');
    }
}
