<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterScheduleCustomerRequirements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_customer_requirements', function (Blueprint $table) {
            $table->string('require_security_clearance')->nullable()->after('length_of_shift');
            $table->integer('security_clearance_level')->unsigned()->nullable()->after('require_security_clearance');
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
            $table->dropColumn('require_security_clearance');
            $table->dropColumn('security_clearance_level');
        });
    }
}
