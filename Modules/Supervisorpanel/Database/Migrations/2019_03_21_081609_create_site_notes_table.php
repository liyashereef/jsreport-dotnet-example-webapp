<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned()->comments('ID from customer');
            $table->string('subject', 100);
            $table->string('attendees', 200);
            $table->string('location', 200);
            $table->text('notes');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('site_notes', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_notes', function (Blueprint $table) {
            $table->dropForeign('site_notes_customer_id_foreign');
        });
        Schema::dropIfExists('site_notes');
    }
}

