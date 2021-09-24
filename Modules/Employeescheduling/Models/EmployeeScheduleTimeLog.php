<?php

namespace Modules\Employeescheduling\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeScheduleTimeLog extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'employee_schedule_id',
        'payperiod_id',
        'user_id',
        'week',
        'date',
        'start_datetime',
        'end_ datetime',
        'hours',
        'approved',
        'approved_by',
        'approved_date',
        'created_by',
        'overlaps',
    ];

    public function schedule()
    {
        return $this->belongsTo('Modules\Employeescheduling\Models\EmployeeSchedule', 'employee_schedule_id')->withTrashed();
    }

    public function employee()
    {
        return $this->hasOne('Modules\Admin\Models\Employee', 'user_id', 'user_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

    public function payperiod()
    {
        return $this->hasOne('Modules\Admin\Models\PayPeriod', 'id', 'payperiod_id')->withTrashed();
    }

    public function employeeshiftpayperiods()
    {
        return $this->belongsTo(
            'Modules\Timetracker\Models\EmployeeShiftPayperiod',
            'pay_period_id',
            'payperiod_id'
        )->withTrashed();
    }
}
