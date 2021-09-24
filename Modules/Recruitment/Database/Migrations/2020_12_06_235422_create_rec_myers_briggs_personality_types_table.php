<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecMyersBriggsPersonalityTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_myers_briggs_personality_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ei')->comment('Extraversion/Introversion');
            $table->string('sn')->comment('Sensing/Intuitive');
            $table->string('tf')->comment('Thinking/Feeling');
            $table->string('jp')->comment('Judging/Perceiving');
            $table->string('type');
            $table->longText('description')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_myers_briggs_personality_types');
    }
}
