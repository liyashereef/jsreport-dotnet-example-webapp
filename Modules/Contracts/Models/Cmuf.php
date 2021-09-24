<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Contracts\Models\ClientContactInformation;


class Cmuf extends Model
{
    use SoftDeletes;
    protected $table = "cmufs";
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'contract_name', 'contract_number', 'submission_date', 'area_manager_id',
        'reason_for_submission', 'business_segment', 'line_of_business', 'multidivisioncontract',
        'lead_division', 'master_entity', 'parent_customer', 'area_manager', 'area_manager_position_text', 'area_manager_email_address',
        'area_manager_office_number', 'area_manager_cell_number', 'area_manager_fax_number', 'office_address',
        'sales_employee_id', 'sales_contact_job_title', 'sales_contact_emailaddress', 'sales_office_number', 'sales_cell_number',
        'sales_contact_faxno', 'sales_contact_division', 'sales_contact_office_address',
        'contract_startdate', 'contract_length', 'contract_enddate', 'renewable_contract', 'contract_length_renewal_years', 'billing_ratechange', 'contract_annualincrease_allowed', 'contractonourtemplate',
        'termination_clause_client', 'terminationnoticeperiodclient', 'termination_clause', 'terminationnoticeperiod',
        'rfc_pricing_tamplate_attachment_id', 'total_annual_contract_billing', 'total_annual_contract_wages_benifits', 'total_annual_expected_contribution_margin', 'total_hours_perweek', 'average_billrate', 'average_wagerate', 'average_markup',
        'contract_billing_cycle', 'contract_payment_method', 'ponumber', 'pocompanyname', 'poattentionto', 'pomailingaddress', 'potitle', 'pocity', 'popostalcode', 'pophone', 'poemail', 'pocellno', 'pofax', 'ponotes', 'po_attachment', 'supervisorassigned',
        'supervisoremployeenumber', 'employeename', 'viewtrainingperformance', 'employeecellphone', 'employeeemailaddress', 'employeetelephone', 'employeefaxno', 'contractcellphoneprovider',
        'supervisortabletrequired', 'supervisorcgluser', 'supervisorpublictransportrequired', 'direction_nearest_intersection', 'department_at_site', 'delivery_hours', 'supervisorcanmailbesent',
        'contractdeviceaccess', 'scopeofwork', 'contract_attachment_id', 'created_by'
    ];


    public function savecontract($data)
    {
        try {
            $datarow = Cmuf::create($data);

            $id = $datarow->id;
            //dd();
            return $id;
        } catch (Exception $th) {
            throw $th;
        }
    }

    public function getBillingFrequency()
    {

        $result =  $this->hasOne('Modules\Admin\Models\ContractBillingCycle', 'id', 'contract_billing_cycle')->withTrashed();

        return $result;
    }

    public function getBillingratechange()
    {

        return $this->hasOne('Modules\Admin\Models\ContractBillingRateChange', 'id', 'billing_ratechange')->withTrashed();
    }

    public function client_contact_information()
    {

        return $this->hasMany('Modules\Contracts\Models\ClientContactInformation', 'contractid', 'id')->withTrashed();
    }

    public function client_contact_information_without_trash()
    {

        return $this->hasMany('Modules\Contracts\Models\ClientContactInformation', 'contractid', 'id');
    }

    public function getParentcustomer()
    {

        return $this->hasOne('Modules\Admin\Models\ParentCustomer', 'id', 'parent_customer')->withTrashed();
    }



    public function getPaymentmethod()
    {
        return $this->hasOne('Modules\Admin\Models\PaymentMethod', 'id', 'contract_payment_method')->withTrashed();
    }

    public function getContractname()
    {
        return $this->hasOne('Modules\Admin\Models\Customer', 'id', 'contract_name')->withTrashed();
    }

    public function getSupervisorname()
    {
        return $this->hasOne('Modules\Admin\Models\User', 'id', 'supervisoremployeenumber')->withTrashed();
    }

    public function getSalesuser()
    {
        return $this->hasOne('Modules\Admin\Models\User', 'id', 'sales_employee_id')->withTrashed();
    }

    public function getSupervisoremployees()
    {
        return $this->hasOne('Modules\Admin\Models\Employee', 'user_id', 'supervisoremployeenumber')->withTrashed();
    }

    public function getPreparedby()
    {
        return $this->hasOne('Modules\Admin\Models\User', 'id', 'created_by')->withTrashed();
    }

    public function getCellphoneprovider()
    {
        return $this->hasOne('Modules\Admin\Models\ContractCellPhoneProvider', 'id', 'contractcellphoneprovider')->withTrashed();
    }

    public function getSupervisordeviceaccess()
    {
        return $this->hasOne('Modules\Admin\Models\DeviceAccess', 'id', 'contractdeviceaccess')->withTrashed();
    }

    public function getPositiontitle()
    {
        return $this->hasOne('Modules\Admin\Models\PositionLookup', 'id', 'sales_contact_job_title')->withTrashed();
    }

    public function getOfficeAddressareamanager()
    {
        return $this->hasOne('Modules\Admin\Models\OfficeAddress', 'id', 'office_address')->withTrashed();
    }

    public function getOfficeAddresssalesmanager()
    {
        return $this->hasOne('Modules\Admin\Models\OfficeAddress', 'id', 'sales_contact_office_address')->withTrashed();
    }

    public function getBusinesssegment()
    {
        return $this->hasOne('Modules\Admin\Models\BusinessSegment', 'id', 'business_segment')->withTrashed();
    }

    public function getBusinessline()
    {
        return $this->hasOne('Modules\Admin\Models\LineOfBusiness', 'id', 'line_of_business')->withTrashed();
    }

    public function getLeadDivisionlookup()
    {
        return $this->hasOne('Modules\Admin\Models\DivisionLookup', 'id', 'lead_division')->withTrashed();
    }

    public function getReasonforsubmission()
    {
        return $this->hasOne('Modules\Admin\Models\ContractSubmissionReason', 'id', 'reason_for_submission')->withTrashed();
    }

    public function getSalesDivisionlookup()
    {
        return $this->hasOne('Modules\Admin\Models\DivisionLookup', 'id', 'sales_contact_division')->withTrashed();
    }
}
