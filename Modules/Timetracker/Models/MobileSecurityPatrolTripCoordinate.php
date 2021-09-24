<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class MobileSecurityPatrolTripCoordinate extends Model
{
    protected $fillable = [];

    public function trip()
    {
        return $this->belongsTo('Modules\Timetracker\Models\MobileSecurityPatrolTrip','mobile_security_patrol_trips_id','id');
    }
}
