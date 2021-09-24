<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayPeriod extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['year', 'pay_period_name', 'short_name', 'start_date', 'week_one_end_date', 'week_two_start_date', 'end_date'];

    public function employeeshiftpayperiods()
    {
        return $this->hasMany(
            'Modules\Timetracker\Models\EmployeeShiftPayperiod',
            'pay_period_id',
            'id'
        );
    }
}
