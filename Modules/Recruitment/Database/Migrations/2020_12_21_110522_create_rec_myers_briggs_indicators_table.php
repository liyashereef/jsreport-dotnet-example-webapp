<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecMyersBriggsIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_myers_briggs_indicators', function (Blueprint $table) {
            $table->increments('id');
            $table->text('value')->nullable()->comment('Indicator Expansion');
            $table->string('initial')->nullable()->comment('Indicator shortname');
            $table->integer('column')->nullable();
            $table->string('option')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_myers_briggs_indicators');
    }
}
