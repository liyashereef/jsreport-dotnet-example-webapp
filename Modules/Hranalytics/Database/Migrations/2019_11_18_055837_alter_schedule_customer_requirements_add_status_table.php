<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterScheduleCustomerRequirementsAddStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_customer_requirements', function (Blueprint $table) {
            $table->integer('status')->default(0)->after('security_clearance_level')->comments('0-Default 1-Open 2-Closed');
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
            $table->dropColumn('status');
        });
    }
}
