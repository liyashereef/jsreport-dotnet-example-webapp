<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeoffCategoryLookupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeoff_category_lookup', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('description',1000);
            $table->string('reference');
            $table->integer('allowed_days')->unsigned();
            $table->integer('allowed_weeks')->unsigned();
            $table->integer('allowed_hours')->unsigned();
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
        Schema::dropIfExists('timeoff_category_lookup');
    }
}
