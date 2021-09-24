<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQrDailyActivityReportToCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('qr_daily_activity_report')->default(0)->after('qr_duration');
            $table->text('qr_recipient_email')->nullable()->after('qr_daily_activity_report');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('qr_daily_activity_report');
            $table->dropColumn('qr_recipient_email');
        });
    }
}
