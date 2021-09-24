<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToEmployeeShiftPayperiodsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('employee_shift_payperiods', function(Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('employee_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('employee_shift_payperiods', function(Blueprint $table) {
            $table->dropForeign('employee_shift_payperiods_customer_id_foreign');
            $table->dropForeign('employee_shift_payperiods_employee_id_foreign');
        });
    }

}
