<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherCategoryLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_category_lookups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category_name', 255);
            $table->string('shortname',255)->nullable();
            $table->integer('document_type_id')->default(3);
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
        Schema::dropIfExists('other_category_lookups');
    }
}
