<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventLogEntry extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'user_id', 'date', 'time', 'duty_officer_id', 'status', 'project_number_id', 'type', 'site_rate', 'accepted_rate', 'start_date', 'end_date', 'schedule_customer_requirement_id', 'time_scheduled', 'requirements_notes', 'status_notes', 'length_of_shift', 'score', 'multiple_shift_id'];

    public function dutyofficer()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'duty_officer_id', 'id');

    }
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');

    }
    public function shift()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts', 'multiple_shift_id', 'id');

    }
    public function status_log()
    {
        return $this->belongsTo('Modules\Admin\Models\StatusLogLookup', 'status', 'id');

    }
    public function project()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Customer', 'project_number_id', 'id');

    }
    public function assignment_type()
    {
        return $this->belongsTo('Modules\Admin\Models\ScheduleAssignmentTypeLookup', 'type', 'id');

    }

    public function requirement()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\ScheduleCustomerRequirement', 'schedule_customer_requirement_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');

    }

    public function trashed_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

}
