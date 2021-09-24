<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTemplateQuestionsCategoriesTableAddSafetyColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_questions_categories', function (Blueprint $table) {
            $table->boolean("safety_type")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_questions_categories', function (Blueprint $table) {
            $table->dropColumn("safety_type");
        });
    }
}
