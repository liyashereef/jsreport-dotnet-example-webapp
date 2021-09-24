<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('project_number', 255)->nullable();
            $table->string('client_name', 255)->nullable();
            $table->string('contact_person_name', 255)->nullable();
            $table->string('contact_person_email_id', 100)->nullable();
            $table->string('contact_person_phone', 255)->nullable();
            $table->string('contact_person_phone_ext', 255)->nullable();
            $table->string('contact_person_cell_phone', 255)->nullable();
            $table->string('contact_person_position', 191)->nullable();
            $table->string('requester_name', 255)->nullable();
            $table->string('requester_position', 255)->nullable();
            $table->string('requester_empno', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 191);
            $table->string('postal_code', 255)->nullable();
            $table->string('geo_location_lat', 191)->nullable();
            $table->string('geo_location_long', 191)->nullable();
            $table->string('radius', 191)->nullable();
            $table->boolean('active')->default(1);
            $table->string('description', 255)->nullable();
            $table->date('proj_open')->nullable();
            $table->string('arpurchase_order_no', 255)->nullable();
            $table->string('arcust_type', 100)->nullable();
            $table->boolean('stc')->default(0);
            $table->date('inquiry_date')->nullable();
            $table->time('time_stamp')->nullable();
            $table->integer('duty_officer_id')->unsigned()->nullable();
            $table->integer('industry_sector_lookup_id')->unsigned()->nullable();
            $table->integer('region_lookup_id')->unsigned()->nullable();
            $table->boolean('shift_journal_enabled')->default(0);
            $table->string('shift_journal_duration')->nullable();
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
        Schema::dropIfExists('customers');
    }

}
