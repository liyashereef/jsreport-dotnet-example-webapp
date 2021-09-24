<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingUserContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_user_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comments('ID from user');
            $table->integer('course_content_id')->unsigned()->comments('ID from course_contents');
            $table->boolean('completed')->default(0);
            $table->string('completed_length')->default(0);
            $table->string('completed_percentage')->default(0);
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
        Schema::dropIfExists('training_user_contents');
    }
}
