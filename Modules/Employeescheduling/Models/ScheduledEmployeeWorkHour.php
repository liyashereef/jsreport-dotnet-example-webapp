<?php

namespace Modules\Employeescheduling\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledEmployeeWorkHour extends Model
{
    protected $fillable = ['employee_schedule_id','payperiod_id','user_id','week','workhours'];
}
