<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSheetApprovalRating extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_shift_payperiod_id',
        'timesheet_approval_payperiod_rating_id',
        'payperiod_id',
        'user_id',
        'deadline_datetime',
        'rating',
        'latest_approved_by',
        'approved_datetime',
        'is_rating_calculated'
    ];

    public function shiftPayperiod()
    {

        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShiftPayperiod', 'employee_shift_payperiod_id', 'id')->whereActive(true);

    }

    public function  employeeRating(){

        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'rating', 'id');
    }

    public function payperiod()
    {
        return $this->belongsTo('Modules\Admin\Models\PayPeriod', 'payperiod_id', 'id')->withTrashed();
        //
    }

    public function users()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

    public function timesheetApprovalPayPeriodRating()
    {
        return $this->belongsTo('Modules\Timetracker\Models\TimesheetApprovalPayperiodRating', 'timesheet_approval_payperiod_rating_id', 'id');
    }

}
