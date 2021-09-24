<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{

    protected $connection = 'mysql';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'employee_id',
        'start',
        'end',
        'work_hours',
        'notes',
        'assigned',
        'live_status_id',
        'shift_type_id',
        'given_end_time',
        'mobile_security_patrol_incident_reported',
        'submitted',
        'employee_shift_payperiod_id',
        'employee_schedule_time_log_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id')->whereActive(true);
    }
    public function user_trashed()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id')->withTrashed();
    }

    public function shift_payperiod()
    {

        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShiftPayperiod', 'employee_shift_payperiod_id', 'id')->whereActive(true);
        //
    }

    public function submitted_shift_payperiod()
    {

        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShiftPayperiod', 'employee_shift_payperiod_id', 'id')->whereSubmitted(true)->whereActive(true);
        //
    }

    /**
     * A shift has many shift jornals
     *
     * @return void
     */
    public function shiftJournals()
    {
        return $this->hasMany('Modules\Timetracker\Models\ShiftJournal', 'shift_id', 'id');
    }

    /**
     * A shift has many Guard Tours
     *
     * @return void
     */
    public function guardTours()
    {
        return $this->hasMany('Modules\Timetracker\Models\GuardTour', 'shift_id', 'id');
    }

    /**
     * A shift has many mobile patrol security trips
     *
     * @return void
     */
    public function trips()
    {
        return $this->hasMany('Modules\Timetracker\Models\MobileSecurityPatrolTrip', 'shift_id', 'id');
    }
    public function latest_meeting_note()
    {
        return $this->hasOne('Modules\Timetracker\Models\ShiftMeetingNote', 'shift_id')->latest();

    }
    public function qrcode_details_with_shift()
    {
        return $this->hasMany('Modules\Timetracker\Models\CustomerQrcodeWithShift', 'shift_id', 'id');
    }

    public function qrcode_history()
    {
        return $this->belongsTo('Modules\Timetracker\Models\CustomerQrcodeHistory', 'id', 'shift_id');
    }

    public function qrcode_summary()
    {
        return $this->belongsTo('Modules\Timetracker\Models\CustomerQrcodeSummary', 'id', 'shift_id');
    }

    public function geofence_data()
    {
        return $this->hasMany('Modules\Timetracker\Models\MobileSecurityPatrolFenceData', 'shift_id', 'id');
    }

    public function geofence_meta()
    {
        return $this->hasOne(MobileSecurityPatrolFenceMeta::class, 'shift_id');
    }

    public function geofence_summary()
    {
        return $this->hasMany(MobileSecurityPatrolFenceSummary::class, 'shift_id');
    }

    public function vehicle_details()
    {
        return $this->belongsTo('Modules\Vehicle\Models\VehicleTrip', 'id', 'shift_id');
    }

    public function employeeScheduleTimeLog()
    {
        return $this->belongsTo('Modules\Employeescheduling\Models\EmployeeScheduleTimeLog', 'employee_schedule_time_log_id', 'id');
    }

}
