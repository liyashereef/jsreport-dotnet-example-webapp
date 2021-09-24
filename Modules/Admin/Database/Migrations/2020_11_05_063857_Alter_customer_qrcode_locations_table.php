<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCustomerQrcodeLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_qrcode_locations', function (Blueprint $table) {
            $table->string('no_of_attempts_week_ends')->nullable();
            $table->string('tot_no_of_attempts_week_day')->nullable();
            $table->string('tot_no_of_attempts_week_ends')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_qrcode_locations', function (Blueprint $table) {
            $table->dropColumn('no_of_attempts_week_ends');
            $table->dropColumn('tot_no_of_attempts_week_day');
            $table->dropColumn('tot_no_of_attempts_week_ends');
        });
    }
}
