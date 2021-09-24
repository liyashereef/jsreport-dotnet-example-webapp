<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManageContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manage_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 100);
            $table->boolean('video')->default(0);
            $table->boolean('attachment')->default(0);
            $table->dateTime('expiry_date')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('manage_contents');
    }
}
