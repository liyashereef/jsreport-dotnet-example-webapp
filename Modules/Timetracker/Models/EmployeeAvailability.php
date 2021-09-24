<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAvailability extends Model
{
    protected $fillable = ['employee_id', 'week_day', 'shift_timing_id', 'created_by'];
    use SoftDeletes;
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id')->whereActive(true);
    }

    public function employee()
    {
        return $this->belongsTo('Modules\Admin\Models\Employee', 'employee_id', 'user_id')->whereActive(true);
    }

    public function createdUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id');
    }

}
