<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->integer('ids_office_id');
            $table->integer('ids_service_id');
            $table->integer('ids_office_slot_id');
            $table->date('slot_booked_date');
            $table->smallInteger('given_interval')->nullable();
            $table->decimal('given_rate', 8, 2)->default(0);
            $table->boolean('to_be_rescheduled')->default(false);
            $table->boolean('is_rescheduled')->default(false);
            $table->date('rescheduled_at')->nullable();
            $table->integer('rescheduled_id')->nullable();
            $table->integer('rescheduled_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('ids_entries');
    }
}
