<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerReportAdhoc extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = ['employee_id', 'date', 'hours_off', 'reason_id', 'notes', 'customer_payperiod_template_id', 'payperiod_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function employee()
    {

        return $this->belongsTo('Modules\Admin\Models\Employee', 'employee_id', 'user_id');
        //
    }

    public function leave_reason()
    {

        return $this->belongsTo('Modules\Admin\Models\LeaveReason', 'reason_id', 'id')->withTrashed();
        //
    }
    public function payperiod()
    {

        return $this->belongsTo('Modules\Admin\Models\PayPeriod', 'payperiod_id', 'id')->withTrashed();
        //
    }
    public function customer_payperiod_template()
    {
        return $this->belongsTo('Modules\Supervisorpanel\Models\CustomerPayperiodTemplate', 'customer_payperiod_template_id', 'id');
        //
    }

}

