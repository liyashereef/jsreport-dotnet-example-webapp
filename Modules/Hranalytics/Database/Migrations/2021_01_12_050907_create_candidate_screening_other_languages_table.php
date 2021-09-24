<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidateScreeningOtherLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_screening_other_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->enum('speaking', ['D - No Knowledge.', 'C - Fluent - this is my native language.', 'B - Functional - this is my second language but I can get by.', 'A - Limited - I am just learning the language.']);
            $table->enum('reading', ['D - No Knowledge.', 'C - Fluent - this is my native language.', 'B - Functional - this is my second language but I can get by.', 'A - Limited - I am just learning the language.']);
            $table->enum('writing', ['D - No Knowledge.', 'C - Fluent - this is my native language.', 'B - Functional - this is my second language but I can get by.', 'A - Limited - I am just learning the language.']);
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
        Schema::dropIfExists('candidate_screening_other_languages');
    }
}
