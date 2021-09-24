<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToEmployeeShiftsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('employee_shifts', function(Blueprint $table) {
            $table->foreign('employee_shift_payperiod_id')->references('id')->on('employee_shift_payperiods')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('employee_shifts', function(Blueprint $table) {
            $table->dropForeign('employee_shifts_employee_shift_payperiod_id_foreign');
        });
    }

}
