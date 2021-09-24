<?php

namespace Modules\Employeescheduling\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSchedule extends Model
{

    use SoftDeletes;

    protected $table = "employee_schedules";
    public $timestamps = true;
    protected $fillable = [
        'customer_id',
        'initial_schedule_id',
        'contractual_hours',
        'avgworkhours',
        'variance',
        'schedindicator',
        'status',
        'status_update_date',
        'status_updated_by',
        'supervisornotes',
        'status_notes',
        'pending_with',
        'created_by',
        'update_by',
        'schedule_overlaps',
    ];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id')->withTrashed();
    }

    public function updatedUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'update_by', 'id')->withTrashed();
    }

    public function createdUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    public function statusUpdatedUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'status_updated_by', 'id')->withTrashed();
    }

    public function scheduleTimeLogs()
    {
        return $this->hasMany('Modules\Employeescheduling\Models\EmployeeScheduleTimeLog', 'employee_schedule_id', 'id')->withTrashed();
    }


    public function reScheduleTimeLogs()
    {
        return $this->hasMany('Modules\Employeescheduling\Models\EmployeeScheduleTimeLog', 'employee_schedule_id', 'initial_schedule_id')->withTrashed();
    }
}
