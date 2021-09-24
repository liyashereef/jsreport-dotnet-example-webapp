<?php

namespace Modules\Timetracker\Models;

use Modules\Admin\Models\User;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CpidRates;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\CpidFunction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Timetracker\Models\EmployeeShiftWorkHourType;
use Modules\Timetracker\Models\WorkHourActivityCodeCustomers;

class EmployeeShiftReportEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payperiod_id',
        'payperiod_week',
        'shift_payperiod_id',
        'user_id',
        'customer_id',
        'cpid_rate_id',
        'cpid_function_id',
        'work_hour_type_id',
        'work_hour_activity_code_customer_id',
        'hours',
        'total_amount',
        'is_manual',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->withTrashed();
    }

    public function payPeriod()
    {
        return $this->belongsTo(PayPeriod::class, 'payperiod_id', 'id')->withTrashed();
    }

    public function cpidRate()
    {
        return $this->belongsTo(CpidRates::class, 'cpid_rate_id', 'id')->withTrashed();
    }

    public function workHourActivityCodeCustomer()
    {
        return $this->belongsTo(WorkHourActivityCodeCustomers::class, 'work_hour_activity_code_customer_id', 'id')->withTrashed();
    }

    public function cpidFunction()
    {
        return $this->belongsTo(CpidFunction::class, 'cpid_function_id', 'id')->withTrashed();
    }

    public function activityType()
    {
        return $this->belongsTo(EmployeeShiftWorkHourType::class, 'work_hour_type_id', 'id')->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

}
