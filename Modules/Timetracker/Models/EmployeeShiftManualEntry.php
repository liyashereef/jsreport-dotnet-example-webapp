<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeShiftManualEntry extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'payperiod_id',
        'payperiod_week',
        'user_id',
        'customer_id',
        'cpid_rate_id',
        'cpid_function_id',
        'work_hour_type_id',
        'work_hour_activity_code_customer_id',
        'hours',
        'total_amount',
        'created_by',
        'updated_by'
    ];
}
