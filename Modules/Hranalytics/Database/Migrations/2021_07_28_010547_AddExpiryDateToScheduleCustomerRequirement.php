<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpiryDateToScheduleCustomerRequirement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_customer_requirements', function (Blueprint $table) {
            $table->dateTime("expiry_date")->nullable()->after("notes");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_customer_requirements', function (Blueprint $table) {
            $table->dropColumn("expiry_date");
        });
    }
}
