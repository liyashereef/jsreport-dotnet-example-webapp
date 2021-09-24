<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniformSchedulingEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniform_scheduling_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('uniform_scheduling_office_id');
            $table->integer('uniform_scheduling_office_timing_id')->nullable();
            $table->date('booked_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('neck_size')->nullable();
            $table->string('chest_size')->nullable();
            $table->string('waist_size')->nullable();
            $table->string('inside_leg_size')->nullable();
            $table->string('hip_size')->nullable();
            $table->smallInteger('given_interval')->nullable();
            $table->decimal('given_rate', 8, 2)->default(0);
            $table->smallInteger('is_client_show_up')->nullable();
            $table->boolean('to_be_rescheduled')->default(false);
            $table->boolean('is_rescheduled')->default(false);
            $table->date('rescheduled_at')->nullable();
            $table->integer('rescheduled_id')->nullable();
            $table->integer('rescheduled_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_canceled')->default(0);
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
        Schema::dropIfExists('uniform_scheduling_entries');
    }
}
