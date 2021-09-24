<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherCategoryName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
            Schema::create('other_category_names', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 500);
                $table->integer('document_type_id')->nullable();
                $table->integer('other_category_lookup_id')->nullable();
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
Schema::dropIfExists('other_category_names');
    }
}
