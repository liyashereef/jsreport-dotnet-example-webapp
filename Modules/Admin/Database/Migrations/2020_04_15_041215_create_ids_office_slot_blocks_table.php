<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsOfficeSlotBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_office_slot_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('day_id')->nullable();
            $table->date('slot_block_date')->nullable();
            $table->integer('ids_office_id')->nullable();
            $table->integer('ids_service_id')->nullable();
            $table->integer('ids_office_slot_id')->nullable();
            $table->integer('created_by')->comment('user_id')->nullable();
            $table->integer('ids_blocking_request_id')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('ids_office_slot_blocks');
    }
}
