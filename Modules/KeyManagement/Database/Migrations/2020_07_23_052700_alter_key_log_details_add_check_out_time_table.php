<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKeyLogDetailsAddCheckOutTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('key_log_details', function (Blueprint $table) {
            $table->dateTime('checked_out_date_time')->nullable()->after('checked_in_from');
            $table->dateTime('checked_in_date_time')->nullable()->after('checked_out_date_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('key_log_details', function (Blueprint $table) {
            $table->dateTime('checked_out_date_time')->nullable()->after('checked_in_from');
            $table->dateTime('checked_in_date_time')->nullable()->after('checked_out_date_time');
        });
    }
}
