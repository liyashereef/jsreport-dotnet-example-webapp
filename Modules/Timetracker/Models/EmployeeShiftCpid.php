<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\CpidLookup;
use App\Services\HelperService;
use Modules\Admin\Models\CpidCustomerAllocations;
use Modules\Admin\Models\CpidRates;

class EmployeeShiftCpid extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'cpid',
        'employee_shift_payperiod_id',
        'employee_id',
        'work_hour_type_id',
        'activity_code_id',
        'hours',
        'total_amount',
        'cpid_rate_id'
    ];

    public function cpid_lookup()
    {
        return $this->belongsTo(CpidLookup::class, 'cpid');
    }

    public function cpid_lookup_with_trash()
    {
        return $this->belongsTo(CpidLookup::class, 'cpid')->withTrashed();
    }

    public function cpid_customer_allocation()
    {
        return $this->hasMany(CpidCustomerAllocations::class, 'cpid', 'cpid');
    }

    public function getFormattedTimeAttribute()
    {
        return HelperService::formatedTimeString($this->hours);
    }
    public function cpid_rates_with_trash()
    {
        return $this->hasOne('Modules\Admin\Models\CpidRates', 'id', 'cpid_rate_id')->withTrashed();
    }

    public function shift_work_hour_type()
    {
        return $this->belongsTo(EmployeeShiftWorkHourType::class, 'work_hour_type_id')->select('id', 'name');
    }


    public function shift_work_hour_type_withtrashed()
    {
        return $this->belongsTo(EmployeeShiftWorkHourType::class, 'work_hour_type_id')->select('id', 'name')->withTrashed();
    }

    public function activity_code_withtrashed()
    {
        return $this->belongsTo(WorkHourActivityCodeCustomer::class, 'activity_code_id')->select('id', 'code')->withTrashed();
    }

    public function employee_shift_payperiod()
    {
        return $this->belongsTo(EmployeeShiftPayperiod::class, 'employee_shift_payperiod_id');
    }

    public function getRateNumericAttribute()
    {
        $cpidRate = $this->cpid_rates_with_trash;

        // switch ($this->work_hour_type_id) {
        //     case 1:
        //         return (float) $cpidRate->p_standard;
        //     case 2:
        //         return (float) $cpidRate->p_overtime;
        //     case 3:
        //         return (float) $cpidRate->p_holiday;
        //     default:
        //         return 0;


        // For payroll automation
        return (float) $cpidRate->p_standard;
    }
    public function getRateStringWithCurrencyAttribute()
    {
        $rate = $this->rate_numeric;
        return ($rate) ? '$' . number_format($rate, 2, '.', '') : '';
    }
}
