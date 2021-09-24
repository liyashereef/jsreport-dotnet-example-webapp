<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterUraTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ura_transactions', function (Blueprint $table) {
            $table->unsignedInteger('uniform_order_id')
                ->nullable()
                ->after('employee_shift_report_entry_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ura_transactions', function (Blueprint $table) {
            $table->dropColumn('uniform_order_id');
        });
    }
}
