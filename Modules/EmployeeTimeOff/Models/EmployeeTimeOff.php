<?php

namespace Modules\EmployeeTimeOff\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTimeOff extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    public $table = 'employee_time_off';
    protected $fillable = ['employee_id', 'employee_role_id', 'customer_id', 'supervisor_id', 'areamanager_id', 'oc_email', 'hr_id', 'request_type_id', 'vacation_pay_required', 'vacation_pay_amount', 'vacation_payperiod_id', 'start_date', 'end_date', 'no_of_shifts', 'average_shift_length', 'total_hours_away', 'leave_reason_id', 'other_reason', 'nature_of_request', 'request_category_id', 'days_requested', 'days_approved', 'days_rejected', 'days_remaining', 'hr_approved', 'approved_by', 'approved', 'current_level', 'pending_with_emp', 'created_by', 'updated_by'];

    public function employee()
    {
        return $this->belongsTo('Modules\Admin\Models\Employee', 'employee_id', 'user_id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }


    public function hr()
    {
        return $this->belongsTo('Modules\Admin\Models\Employee', 'hr_id', 'user_id')->withTrashed();
    }
    public function log()
    {
        return $this->hasMany('Modules\EmployeeTimeOff\Models\TimeOffLog', 'time_off_id', 'id');
    }
    public function latestLog()
    {
        return $this->hasOne('Modules\EmployeeTimeOff\Models\TimeOffLog', 'time_off_id', 'id')->latest();
    }
    public function request_type()
    {
        return $this->belongsTo('Modules\Admin\Models\TimeOffRequestTypeLookup', 'request_type_id', 'id')->withTrashed();
    }
    public function leave_reason()
    {
        return $this->belongsTo('Modules\Admin\Models\LeaveReason', 'leave_reason_id', 'id')->withTrashed();
    }
    public function category()
    {
        return $this->belongsTo('Modules\Admin\Models\TimeOffCategoryLookup', 'request_category_id', 'id')->withTrashed();
    }
    public function payperiod()
    {
        return $this->belongsTo('Modules\Admin\Models\PayPeriod', 'vacation_payperiod_id', 'id')->withTrashed();
    }

    public function attachments()
    {
        return $this->hasMany('Modules\EmployeeTimeOff\Models\TimeoffAttachment','timeoff_id','id');
    }

     public function employee_timeoff_workflow()
    {
        return $this->hasMany('Modules\EmployeeTimeOff\Models\EmployeeTimeOffWorkflow', 'emp_role_id', 'employee_role_id');
    }

     public function employee_timeoff_level()
    {
        return $this->hasMany('Modules\EmployeeTimeOff\Models\EmployeeTimeOffWorkflow', 'level', 'current_level');
    }

    
}
