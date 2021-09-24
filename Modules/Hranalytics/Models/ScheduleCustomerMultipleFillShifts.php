<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleCustomerMultipleFillShifts extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['schedule_customer_requirement_id', 'assigned_employee_id', 'shift_timing_id', 'shift_from', 'shift_to', 'assigned', 'assigned_by', 'parent_id', 'no_of_position'];

    /**
     * Schedule Customer Requirement description
     * @return void
     */
    public function scheduleCustomerRequirement()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\ScheduleCustomerRequirement', 'schedule_customer_requirement_id', 'id');
    }

    /**
     * Shift Timing
     *
     * @return void
     */
    public function shiftTiming()
    {
        return $this->belongsTo('Modules\Admin\Models\ShiftTiming', 'shift_timing_id', 'id');
    }

    /**
     * User function
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'assigned_employee_id', 'id');
    }

    /**
     * Trashed User function
     *
     * @return void
     */
    public function trashed_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'assigned_employee_id', 'id')->withTrashed();
    }

    public function parentShift()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts', 'parent_id', 'id');
    }

    public function latestEventLog()
    {
        return $this->hasOne('Modules\Hranalytics\Models\EventLogEntry', 'multiple_shift_id', 'id')->latest();
    }
}
