<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractsHolidayPaymentAgreement extends Model
{
    use SoftDeletes;
    protected $table = "contracts_holiday_payment_agreements";
    protected $fillable = ['contract_id','holiday_id','paymentstatus_id'];
    protected $dates = ['deleted_at'];

    public function getHoliday(){
        return $this->hasOne('Modules\Admin\Models\StatHolidays','id','holiday_id')->withTrashed();
    }

    public function getHolidaypayment(){
        return $this->hasOne('Modules\Admin\Models\HolidayPaymentAllocation','id','paymentstatus_id')->withTrashed();
    }

    
}
