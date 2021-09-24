<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'project_number',
        'client_name',
        'contact_person_name',
        'master_customer',
        'contact_person_email_id',
        'contact_person_phone',
        'contact_person_position',
        'contact_person_phone_ext',
        'contact_person_cell_phone',
        'requester_name',
        'requester_position',
        'requester_empno',
        'address', 'city',
        'province',
        'postal_code',
        'billing_address',
        'geo_location_lat',
        'geo_location_long',
        'radius', 'active',
        'description',
        'proj_open',
        'proj_expiry',
        'arpurchase_order_no',
        'arcust_type',
        'stc',
        'inquiry_date',
        'time_stamp',
        'duty_officer_id',
        'industry_sector_lookup_id',
        'region_lookup_id',
        'guard_tour_enabled',
        'guard_tour_duration',
        'show_in_sitedashboard',
        'overstay_enabled',
        'overstay_time',
        'shift_journal_enabled',
        'time_shift_enabled',
        'basement_mode',
        'basement_interval',
        'geo_fence',
        'geo_fence_satellite',
        'mobile_security_patrol_site',
        'basement_noofrounds',
        'created_by',
        'updated_by',
        'incident_report_logo',
        'contractual_visit_unit',
        'fence_interval',
        'employee_rating_response',
        'employee_rating_response_time',
        'qr_patrol_enabled',
        'key_management_enabled',
        'qr_picture_limit',
        'qr_interval_check',
        'qr_duration',
        'key_management_signature',
        'key_management_image_id',
        'facility_booking',
        'motion_sensor_enabled',
        'motion_sensor_incident_subject',
        'visitor_screening_enabled',
        'time_sheet_approver_id',
        'customer_type_id',
        'rec_onboarding_threshold_days',
        'recruiting_match_score_for_sending_mail',
        'qr_daily_activity_report',
        'qr_recipient_email'

    ];

    protected $appends = ['client_name_and_number', 'incident_logo_path_with_fallback'];

    public function customerPayperiodTemplate()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\CustomerPayperiodTemplate');
    }

    public function latestContract()
    {
        return $this->hasOne('Modules\Contracts\Models\Cmuf', "contract_name")->latest();
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
        return $this->hasOne(
            'Modules\Admin\Models\CustomerEmployeeAllocation',
            'customer_id',
            'id'
        )
            ->with('supervisor', 'supervisor.employee')
            ->whereHas('supervisor')->latest();
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
    /**
     * Get all cpids of a customer.
     */
    //todo::CpidCustomer
    public function cpids()
    {
        return $this->hasMany('Modules\Admin\Models\CpidCustomerAllocations', 'customer_id', 'id');
    }

    public function getIncidentLogoPathWithFallbackAttribute()
    {
        if (Storage::disk('public')->exists($this->incident_report_logo)) {
            return Storage::disk('public')->path($this->incident_report_logo);
        }
        return public_path() . '/images/CGL-LOGO-600px-152px.png';
    }

    public function qrcodeLocations()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerQrcodeLocation', 'customer_id', 'id');
    }

    public function geoFenceDetails()
    {
        return $this->hasMany('Modules\Admin\Models\Geofence', 'customer_id', 'id')->where('active', 1);
    }

    /**
     * Get project name and number
     *
     * @return void
     */
    public function getCustomerNameAndNumberAttribute()
    {
        return $this->project_number . ' - ' . $this->client_name;
    }

    public function customerDetails()
    {
        return $this->hasOne('Modules\Admin\Models\CustomerTemplateEmail', 'customer_id', 'id');
    }

    public function feverReading()
    {
        return $this->hasMany('Modules\FeverScan\Models\FeverReading', 'customer_id', 'id');
    }

    public function getGroupByGenderAndAgeFeverReading()
    {
        return $this->hasMany('Modules\FeverScan\Models\FeverReading', 'customer_id', 'id')
            ->select('gender', 'age', \DB::raw('count(*) as total'))
            ->groupBy('gender', 'age');
    }

    /**
     * Get project name and number
     *
     * @return void
     */
    public function getClientNameAndNumberAttribute()
    {
        return $this->client_name . ' (' . $this->project_number . ')';
    }

    public function projects()
    {
        return $this->hasMany('Modules\ProjectManagement\Models\PmProject', 'customer_id', 'id');
    }

    public function task()
    {
        return $this->hasMany('Modules\ProjectManagement\Models\PmTask', 'site_id', 'id');
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->with([$relation => $constraint])->whereHas($relation, $constraint);
    }

    public function subjectAllocation()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerIncidentSubjectAllocation', 'customer_id', 'id');
    }

    public function customerPriority()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerIncidentPriority', 'customer_id', 'id');
    }

    public function regionLookUp()
    {
        return $this->belongsTo('Modules\Admin\Models\RegionLookup', 'region_lookup_id', 'id');
    }

    public function VisitorLogScreeningTemplateCustomerAllocation()
    {
        return $this->hasOne('Modules\Admin\Models\VisitorLogScreeningTemplateCustomerAllocation', 'customer_id', 'id');
    }

    public function timeSheetApproverDetails()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'time_sheet_approver_id', 'id')->with('employee')->withTrashed();
    }

    public function customerEmployeeAllocation()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerEmployeeAllocation', 'customer_id', 'id');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type_id', 'id');
    }
}
