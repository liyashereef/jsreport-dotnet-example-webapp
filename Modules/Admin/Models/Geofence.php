<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;
use Modules\Timetracker\Models\MobileSecurityPatrolFenceData;
use Modules\Timetracker\Models\MobileSecurityPatrolFenceSummary;

class Geofence extends Model
{
    use SoftDeletes;
    protected $table = "geo_fences";
    protected $hidden = [];
    protected $fillable = [
        'title',
        'address',
        'customer_id',
        'geo_lat',
        'geo_lon',
        'geo_rad',
        'visit_count',
        'active',
        'contractual_visit',
        'unit'
    ];
    protected $dates = ['deleted_at'];

    
    public function ContractualVisitUnit()
    {
        return $this->belongsTo(ContractualVisitUnitLookup::class,'unit');
    }

    public function customer_trashed()
    {
        return $this->belongsTo(Customer::class,'customer_id')->withTrashed();
    }
    public function MobileSecurityPatrolFenceData(){
        return $this->hasMany(MobileSecurityPatrolFenceData::class,'fence_id'); 
    }

    public function mobile_security_patrol_fence_summaries(){
        return $this->hasMany(MobileSecurityPatrolFenceSummary::class,'fence_id')
        ->groupBy('fence_id')
        ->select('fence_id',
        \DB::raw('SUM(visit_count_expected) as total_visit_count_expected'),
        \DB::raw('SUM(visit_count_actual) as total_visit_count_actual'));
    }

}
