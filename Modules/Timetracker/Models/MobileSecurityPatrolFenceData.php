<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class MobileSecurityPatrolFenceData extends Model
{

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'shift_id',
        'fence_id',
        'start_coordinate_id',
        'end_coordinate_id',
        'time_entry',
        'time_exit',
        'duration',
        'visited'
    ];

    
    public function shift()
    {
        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift','shift_id','id');
    }

    public function fence()
    {
        return $this->belongsTo('Modules\Admin\Models\Geofence', 'fence_id', 'id');
    }

    public function start_coordinate()
    {
        return $this->belongsTo('Modules\Timetracker\Models\MobileSecurityPatrolTripCoordinate', 'start_coordinate_id', 'id');
    }

    public function end_coordinate()
    {
        return $this->belongsTo('Modules\Timetracker\Models\MobileSecurityPatrolTripCoordinate', 'end_coordinate_id', 'id');
    }


}
