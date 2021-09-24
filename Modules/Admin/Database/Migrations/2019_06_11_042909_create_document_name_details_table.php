<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentNameDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('document_name_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 500);
            $table->integer('document_type_id')->nullable();
            $table->integer('document_category_id')->nullable();
            $table->boolean('is_editable')->default(1);
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
        Schema::dropIfExists('document_name_details');
    }
}
