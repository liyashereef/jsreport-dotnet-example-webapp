<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParentCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_customers', function (Blueprint $table) {
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
            $table->string('province', 191)->nullable();
            $table->string('postal_code', 255)->nullable();
            $table->text('billing_address')->nullable();
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
            $table->boolean('guard_tour_enabled')->default(false);
            $table->string('guard_tour_duration', 191)->nullable();
            $table->boolean('show_in_sitedashboard')->default(true);
            $table->boolean('overstay_enabled')->default(0);
            $table->string('overstay_time', 255)->nullable();
            $table->boolean('shift_journal_enabled')->default(false);
            $table->boolean('time_shift_enabled')->default(false);
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
        Schema::dropIfExists('parent_customers');
    }
}
