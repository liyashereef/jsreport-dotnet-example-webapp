<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminColorsettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_colorsettings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('colorhexacode')->nullable();
            $table->unsignedInteger('fieldidentifier');
            $table->decimal('rangebegin',10,2);
            $table->decimal('rangeend',10,2);
            $table->boolean('status');
            $table->unsignedInteger('created_by');
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
        Schema::dropIfExists('admin_colorsettings');
    }
}
