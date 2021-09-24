<?php

namespace Modules\Employeescheduling\Models;

use \Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use DB;


class EmployeeScheduleAveragePayperiodHours extends Model
{
    public $timestamps = true;
    protected $table = "employee_schedule_payperiod_average_hours";
    protected $fillable = [
        'employee_schedule_id',
        'payperiod_id',
        'user_id',
        'week',
        'workhours'
    ];
}
