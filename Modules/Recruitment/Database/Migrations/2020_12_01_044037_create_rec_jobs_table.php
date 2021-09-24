<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('one who created the job request')->nullable();
            $table->string('unique_key')->unique();
            $table->integer('open_position_id')->unsigned()->comment('position beeing requested');
            $table->integer('no_of_vaccancies')->default(1);
            $table->string('job_description', 10000)->nullable();
            $table->integer('reason_id')->unsigned();
            $table->integer('temp_code_id')->unsigned()->nullable();
            $table->integer('permanent_id')->unsigned()->nullable();
            $table->integer('resign_id')->unsigned()->nullable();
            $table->integer('terminate_id')->unsigned()->nullable();
            $table->string('area_manager', 255)->nullable();
            $table->string('am_email', 255)->nullable();
            $table->date('requisition_date');
            $table->integer('customer_id')->unsigned();
            $table->string('requester', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 13)->nullable();
            $table->string('position', 255)->nullable();
            $table->string('employee_num', 255);
            $table->integer('assignment_type_id')->unsigned()->nullable();
            $table->date('required_job_start_date')->nullable();
            $table->time('time')->nullable();
            $table->string('ongoing', 5)->nullable();
            $table->date('end')->nullable();
            $table->integer('training_id')->unsigned()->nullable();
            $table->string('training_time', 50)->nullable();
            $table->integer('training_timing_id')->unsigned()->nullable();
            $table->string('course', 255)->nullable();
            $table->text('notes')->nullable();
            $table->text('shifts')->nullable();
            $table->text('days_required')->nullable();
            $table->text('criterias')->nullable();
            $table->string('vehicle', 5)->nullable();
            $table->double('wage', 8, 2)->nullable();
            $table->string('hours_per_week')->nullable();
            $table->double('total_experience', 8, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->string('status', 100)->default('pending')->comment('pending, approved,completed,rejected,suspended');
            $table->string('status_reason', 100)->nullable();
            $table->text('required_attachments')->nullable();
            $table->integer('approved_by')->unsigned()->nullable();
            $table->integer('hr_rep_id')->unsigned()->nullable()->comment('HR rep assigned');
            $table->timestamp('approved_at')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_jobs');
    }
}
