<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\Geofence;

class MobileSecurityPatrolFenceSummary extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shift_id',
        'fence_id',
        'visit_count_expected',
        'visit_count_actual',
        'visit_count_missed',
        'visit_count_average',
        'hours_total',
        'hours_average',
        'created_at'
    ];


    public function fence()
    {
        return $this->belongsTo(Geofence::class,'fence_id');
    }

    public function fence_trashed()
    {
        return $this->belongsTo(Geofence::class,'fence_id')->withTrashed();
    }

    public function shift_fence_datas()
    {
        return $this->hasMany(MobileSecurityPatrolFenceData::class,'shift_id','shift_id');
    }
    public function shift()
    {
        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift','shift_id','id');
    }
}
