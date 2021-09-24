<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentCustomer extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['project_number', 'client_name', 'contact_person_name', 'contact_person_email_id', 'contact_person_phone', 'contact_person_position', 'contact_person_phone_ext', 'contact_person_cell_phone', 'requester_name', 'requester_position', 'requester_empno', 'address', 'city', 'province', 'postal_code', 'billing_address', 'geo_location_lat', 'geo_location_long', 'radius', 'active', 'description', 'proj_open', 'arpurchase_order_no', 'arcust_type', 'stc', 'inquiry_date', 'time_stamp', 'duty_officer_id', 'industry_sector_lookup_id', 'region_lookup_id', 'guard_tour_enabled', 'guard_tour_duration', 'show_in_sitedashboard', 'overstay_enabled', 'overstay_time', 'shift_journal_enabled', 'time_shift_enabled', 'created_by', 'updated_by'];
    protected $dates = ['deleted_at'];
    public function customerPayperiodTemplate()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\CustomerPayperiodTemplate');
    }

    /**
     * Get supervisor for a customer
     * @return type
     */
    public function employeeCustomerSupervisor()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerEmployeeAllocation', 'customer_id', 'id')->with('supervisor', 'supervisor.employee')->whereHas('supervisor');
    }

    /**
     * Get latest supervisor for a customer
     * @return type
     */
    public function employeeLatestCustomerSupervisor()
    {
        return $this->hasOne('Modules\Admin\Models\CustomerEmployeeAllocation', 'customer_id', 'id')->with('supervisor', 'supervisor.employee')->whereHas('supervisor')->latest();
    }

    /**
     * Get area manager for a customer
     * @return type
     */
    public function employeeCustomerAreaManager()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerEmployeeAllocation', 'customer_id', 'id')->with('areaManager', 'areaManager.employee')->whereHas('areaManager');
    }

    /**
     * Get latest area manager for a customer
     * @return type
     */
    public function employeeLatestCustomerAreaManager()
    {
        return $this->hasOne('Modules\Admin\Models\CustomerEmployeeAllocation', 'customer_id', 'id')->with('areaManager', 'areaManager.employee')->whereHas('areaManager')->latest();
    }

    public function stcDetails()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CustomerStcDetail', 'customer_id')->with('security_clearance');

    }

    /**
     * Get rating details of customer
     * @return relation
     */
    public function ratingDetails()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\CustomerRating', 'customer_id', 'id')->orderBy('created_at', 'DESC');

    }
    public function employeeShiftPayperiods()
    {
        return $this->hasMany('Modules\Timetracker\Models\EmployeeShiftPayperiod', 'customer_id', 'id')->orderBy('created_at', 'DESC');

    }

    public function requesterDetails()
    {
        return $this->hasOne('Modules\Admin\Models\User', 'id', 'requester_name')->with('employee.employeePosition');

    }

    /**
     * Get supervisor for a customer
     * @return type
     */
    public function visitorLog()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogCustomerTemplateAllocation', 'customer_id', 'id');
    }
}
