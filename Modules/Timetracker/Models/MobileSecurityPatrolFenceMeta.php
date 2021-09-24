<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class MobileSecurityPatrolFenceMeta extends Model
{
    protected $fillable = [
        'shift_id',
        'total_visits',
        'missed',
        'average'
    ];

    public function shift()
    {
        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift','shift_id','id');
    }
}


