<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->enum('speaking', ['D - No Knowledge.', 'C - Fluent - this is my native language.', 'B - Functional - this is my second language but I can get by.', 'A - Limited - I am just learning the language.']);
            $table->enum('reading', ['D - No Knowledge.', 'C - Fluent - this is my native language.', 'B - Functional - this is my second language but I can get by.', 'A - Limited - I am just learning the language.']);
            $table->enum('writing', ['D - No Knowledge.', 'C - Fluent - this is my native language.', 'B - Functional - this is my second language but I can get by.', 'A - Limited - I am just learning the language.']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_languages');
    }
}
