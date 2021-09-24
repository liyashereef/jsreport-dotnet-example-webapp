<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTimeoff extends Model
{
    use SoftDeletes;
    protected $table = "employee_timeoff";
    public $timestamps = true;
    protected $fillable =
    [
        'project_number',
        'project_id',
        'employee_id',
        'cpidRate_id',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'leave_duration',
        'reason_id',
        'backfillstatus',
        'created_by',
        'mail_send'
    ];

    protected $dates = ['deleted_at'];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'project_id')->withTrashed();
    }

    public function reasons()
    {
        return $this->belongsTo('Modules\Admin\Models\TimeOffRequestTypeLookup', 'reason_id', 'id');
        // return $this->belongsTo('Modules\Admin\Models\LeaveReason', 'reason_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id')->withTrashed();
    }

    public function employee()
    {
        return $this->hasOne('Modules\Admin\Models\Employee', 'user_id', 'employee_id')->withTrashed();
    }

    public function cpidRate()
    {
        return $this->belongsTo('Modules\Admin\Models\CpidRates', 'cpidRate_id')->withTrashed();
    }

    public function cpidLookup()
    {
        return $this->belongsTo('Modules\Admin\Models\CpidLookup', 'cpidRate_id')->withTrashed();
    }
}
