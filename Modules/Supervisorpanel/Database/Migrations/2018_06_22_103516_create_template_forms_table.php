<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_id')->unsigned()->index('template_forms_template_id_foreign');
            $table->integer('position');
            $table->integer('question_category_id')->unsigned()->index('template_forms_question_category_id_foreign')->nullable();
            $table->integer('parent_position')->nullable();
            $table->text('question_text');
            $table->integer('answer_type_id')->unsigned()->index('template_forms_answer_type_id_foreign');
            $table->boolean('multi_answer');
            $table->boolean('show_if_yes')->nullable();
            $table->float('score_yes', 8, 4)->default(0)->nullable();
            $table->float('score_no', 8, 4)->default(0)->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('template_forms', function (Blueprint $table) {
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
            $table->foreign('question_category_id')->references('id')->on('template_questions_categories')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('answer_type_id')->references('id')->on('answer_type_lookups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('template_forms');
    }
}
