<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateOpenshiftApplication extends Model
{
    use SoftDeletes;
    protected $fillable = ['shiftid', 'userid', 'customerid', 'multifillid', 'startdate', 'enddate', 'starttime', 'endtime', 'openshifts', 'address', 'siterate', 'lineardistance', 'actualdistance', 'sitenotes', 'status', 'approved_by', 'latitude', 'longitude', 'readflag'];
    protected $dates = ['deleted_at'];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customerid', 'id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'userid', 'id');
    }
    public function approvedUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'approved_by', 'id');
    }
    public function requirement()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\ScheduleCustomerRequirement', 'shiftid', 'id');
    }

    public function scheduleCustomerMultipleFillShift()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts', 'multifillid', 'id');
    }
}
