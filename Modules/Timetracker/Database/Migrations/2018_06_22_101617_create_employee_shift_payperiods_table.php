<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeShiftPayperiodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_shift_payperiods', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pay_period_id')->unsigned()->comment('This will be ID from payperiod table, based on shift start and end');
			$table->integer('employee_id')->unsigned()->index('employee_shift_payperiods_employee_id_foreign');
			$table->integer('customer_id')->unsigned()->index('employee_shift_payperiods_customer_id_foreign');
			$table->boolean('assigned')->default(1);
			$table->boolean('submitted')->default(0);
			$table->time('total_regular_hours')->nullable();
			$table->time('total_overtime_hours')->nullable();
			$table->time('total_statutory_hours')->nullable();
			$table->integer('approved_by')->unsigned()->nullable();
			$table->boolean('approved')->default(0);
			$table->boolean('client_approved_billable_overtime')->default(0);
			$table->boolean('client_approved_billable_statutory')->default(0);
			$table->text('notes', 65535)->nullable();
			$table->text('weekly_performance', 65535)->nullable();
			$table->boolean('active')->default(1);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('employee_shift_payperiods');
	}

}
