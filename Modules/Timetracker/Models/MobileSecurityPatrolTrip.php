<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class MobileSecurityPatrolTrip extends Model
{
    protected $fillable = [];

    public function shift()
    {
        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift','shift_id','id');
    }

    public function tripcoordinates()
    {
        return $this->hasMany('Modules\Timetracker\Models\MobileSecurityPatrolTripCoordinate','mobile_security_patrol_trips_id','id');
    }

   
}
