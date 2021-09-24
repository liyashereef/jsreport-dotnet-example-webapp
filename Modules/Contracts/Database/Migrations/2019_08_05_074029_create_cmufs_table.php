<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmufsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmufs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contract_name');
            $table->string('contract_number');
            $table->date('submission_date');
            $table->unsignedInteger('area_manager_id')->nullable()->default(0);
            $table->unsignedInteger('reason_for_submission')->nullable()->default(0);
            $table->unsignedInteger('business_segment')->nullable()->default(0);
            $table->unsignedInteger('line_of_business')->nullable()->default(0);
            $table->boolean('multidivisioncontract')->nullable()->default(false);
            $table->unsignedInteger('lead_division')->nullable()->default(0);
            $table->boolean('master_entity')->nullable()->default(false);
            $table->unsignedInteger('parent_customer')->nullable()->default(0);
            $table->string('area_manager')->nullable();
            $table->string('area_manager_position_text')->nullable();
            $table->string('area_manager_email_address')->nullable();
            $table->string('area_manager_office_number')->nullable();
            $table->string('area_manager_cell_number')->nullable();
            $table->string('area_manager_fax_number')->nullable();
            $table->unsignedInteger('office_address')->nullable();

            $table->unsignedInteger('sales_employee_id')->nullable()->default(0);
            $table->unsignedInteger('sales_contact_job_title')->nullable()->default(0);
            $table->string('sales_contact_emailaddress')->nullable();
            $table->string('sales_office_number')->nullable();
            $table->string('sales_cell_number')->nullable();
            $table->string('sales_contact_faxno')->nullable();
            $table->unsignedInteger('sales_contact_division')->default(0);
            $table->unsignedInteger('sales_contact_office_address')->default(0);

            $table->date("contract_startdate")->nullable();
            $table->unsignedinteger('contract_length')->default(0);
            $table->date("contract_enddate")->nullable();
            $table->boolean("renewable_contract")->default(false);
            $table->unsignedinteger('contract_length_renewal_years')->default(0);
            $table->unsignedinteger("billing_ratechange")->nullable();
            $table->string("contract_annualincrease_allowed", 50)->nullable();

            $table->unsignedinteger('rfc_pricing_tamplate_attachment_id')->default(0);
            $table->float('total_annual_contract_billing', 15, 2)->nullable();
            $table->float('total_annual_contract_wages_benifits', 15, 2)->nullable();
            $table->float('total_annual_expected_contribution_margin', 15, 2)->nullable();
            $table->unsignedinteger('total_hours_perweek')->default(0)->comment("Total hours per week In Hours not decimal");

            $table->float('average_billrate', 15, 2)->nullable();
            $table->float('average_wagerate', 15, 2)->nullable();
            $table->float('average_markup', 15, 2)->nullable();

            $table->unsignedInteger('contract_billing_cycle')->default(0);
            $table->unsignedInteger('contract_payment_method')->default(0);

            $table->string('ponumber')->nullable();
            $table->string('pocompanyname')->nullable();
            $table->string('poattentionto')->nullable();
            $table->string('potitle')->nullable();
            $table->string('pocity')->nullable();
            $table->string('popostalcode')->nullable();
            $table->string('pophone')->nullable();
            $table->string('poemail')->nullable();
            $table->string('pocellno')->nullable();
            $table->string('pofax')->nullable();
            $table->string('ponotes')->nullable();
            $table->unsignedInteger('po_attachment')->nullable();

            $table->boolean('supervisorassigned')->default(0);
            $table->unsignedInteger('supervisoremployeenumber')->default(0);
            $table->string('employeename')->nullable();
            $table->boolean('viewtrainingperformance')->default(0)->nullable();
            $table->string('employeecellphone')->nullable();
            $table->string('employeeemailaddress')->nullable();
            $table->string('employeetelephone')->nullable();
            $table->string('employeefaxno')->nullable();
            $table->unsignedInteger('contractcellphoneprovider')->nullable();
            $table->unsignedInteger('supervisortabletrequired')->default(0);
            $table->unsignedInteger('supervisorcgluser')->default(0);
            $table->unsignedInteger('supervisorpublictransportrequired')->default(0);
            $table->string('direction_nearest_intersection')->nullable();
            $table->string('department_at_site')->nullable();
            $table->string('delivery_hours')->nullable();
            $table->boolean('supervisorcanmailbesent')->default(false);
            $table->unsignedInteger('contractdeviceaccess')->nullable();
            $table->mediumText('scopeofwork')->nullable();
            $table->unsignedInteger('contract_attachment_id')->default(0);
            $table->unsignedInteger('created_by')->default(0);

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
        Schema::dropIfExists('cmufs');
    }
}
