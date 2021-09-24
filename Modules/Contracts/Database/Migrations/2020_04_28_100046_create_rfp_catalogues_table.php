<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRfpCataloguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfp_catalogues', function (Blueprint $table) {
            $table->increments('id');
            $table->text('topic');
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('customer_id');
            $table->text('description', 5000)->nullable();
            $table->integer('attachment_id')->nullable();
            $table->integer('reviewed_status')
                ->default(null)
                ->nullable()
                ->comments('null-Pending, 0-Rejected, 1-Approved');
            $table->integer('reviewed_by')->nullable();
            $table->date('reviewed_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('');
    }
}
