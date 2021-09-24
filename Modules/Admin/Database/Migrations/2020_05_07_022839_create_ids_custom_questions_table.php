<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsCustomQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_custom_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question',500);
            $table->integer('display_order')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('has_other')->default(false);
            $table->boolean('is_active')->default(false);
            $table->dateTime('deactivated_at')->nullable();
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
        Schema::dropIfExists('ids_custom_questions');
    }
}
