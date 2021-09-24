<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWorkHourCustomerDup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_hour_activity_code_customers', function (Blueprint $table) {
            $table->string('duplicate_code')
            ->nullable()
            ->after('code')
            ->comment('Duplicate export data row with code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_hour_activity_code_customers', function (Blueprint $table) {
            $table->dropColumn('duplicate_code');
        });
    }
}
