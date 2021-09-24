<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleCustomerRequirement extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = [
        'customer_id', 'user_id', 'type', 'site_rate', 'start_date', 'end_date', 'time_scheduled',
        'notes', 'expiry_date', 'length_of_shift', 'require_security_clearance', 'security_clearance_level', 'no_of_shifts', 'overtime_notes', 'fill_type'
    ];

    public function assignment_type()
    {
        return $this->belongsTo('Modules\Admin\Models\ScheduleAssignmentTypeLookup', 'type', 'id');
    }
    public function trashed_assignment_type()
    {
        return $this->belongsTo('Modules\Admin\Models\ScheduleAssignmentTypeLookup', 'type', 'id')->withTrashed();
    }

    public function trashed_fill_type()
    {
        return $this->belongsTo('Modules\Admin\Models\ScheduleAssignmentTypeLookup', 'fill_type', 'id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->with('stcDetails');
    }

    public function multifill()
    {
        return $this->hasMany('Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts', 'schedule_customer_requirement_id', 'id');
    }

    /**
     * To get latest log for the requirement
     *
     * @return void
     */
    public function event_log_entry_latest()
    {
        return $this->hasOne('Modules\Hranalytics\Models\EventLogEntry', 'schedule_customer_requirement_id', 'id')
            ->select('id', 'candidate_id', 'status', 'schedule_customer_requirement_id', 'accepted_rate', 'user_id', 'duty_officer_id', 'status_notes', 'score', \DB::raw('DATE(created_at) as project_closed_date'))
            ->latest();
    }

    /**
     * To get latest log for the requirement
     *
     * @return void
     */
    public function event_log_entry_latest_accepted()
    {
        return $this->hasOne('Modules\Hranalytics\Models\EventLogEntry', 'schedule_customer_requirement_id', 'id')->where('status', 1)
            ->select('id', 'candidate_id', 'status', 'schedule_customer_requirement_id', 'accepted_rate', 'user_id', 'duty_officer_id', 'status_notes', 'score', \DB::raw('DATE(created_at) as project_closed_date'))
            ->latest();
    }

    /**
     * To get all event logs for this requiremnt
     *
     * @return void
     */
    public function eventLogs()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EventLogEntry', 'schedule_customer_requirement_id', 'id');
    }

    /**
     * User function
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }
    /**
     * User function
     *
     * @return void
     */
    public function trashed_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

    /**
     * Belongs relation to securityClearanceLookup
     *
     */
    public function security_clearance()
    {
        return $this->belongsTo('Modules\Admin\Models\SecurityClearanceLookup', 'security_clearance_level', 'id');
    }
    /**
     * Belongs relation to securityClearanceLookup
     *
     */
    public function trashed_security_clearance()
    {
        return $this->belongsTo('Modules\Admin\Models\SecurityClearanceLookup', 'security_clearance_level', 'id')->withTrashed();
    }

    /**
     * To get latest log for the requirement
     *
     * @return void
     */
    public function scheduleCustomerAllShifts()
    {
        return $this->hasMany('Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts', 'schedule_customer_requirement_id', 'id')->with('shiftTiming')->with('trashed_user')->orderBy('shift_from');
    }
    /**
     * To get all event logs for this requiremnt
     *
     * @return void
     */
    public function openshifts()
    {
        return $this->hasMany('Modules\Timetracker\Models\CandidateOpenshiftApplication', 'shiftid', 'id');
    }
}
