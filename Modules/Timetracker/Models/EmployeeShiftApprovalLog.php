<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\User;

class EmployeeShiftApprovalLog extends Model
{
    protected $fillable = ['employee_shift_payperiod_id','cpid','total_regualr_hours','total_overtime_hours','total_statutory_hours','approved_by','notes'];

    public function approved_user()
    {
     return $this->belongsTo(User::class,'approved_by');
    }
}
