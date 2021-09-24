<?php

namespace Modules\Facility\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FacilityService extends Model
{
    use SoftDeletes;
    protected $fillable = ['facility_id','service','description','restrict_booking','active','created_by','updated_by'];

    /**
     * Get the Facilitys's data.
     */
    public function facilitydata()
    {
        return $this->morphOne('Modules\Facility\Models\FacilityServiceData', 'model');
    }
    /**
     * Get the Facilitys's data.
     */
    public function FacilityServiceDataMany()
    {
        return $this->morphMany('Modules\Facility\Models\FacilityServiceData', 'model');
    }

    /**
     * Get the Facilitys's timing.
     */
    public function facilityserviceuserallocation()
    {
        return $this->morphMany('Modules\Facility\Models\FacilityServiceUserAllocation', 'model');
    }

    /**
     * Get the Facilitys's timing.
     */
    public function facilitytiming()
    {
        return $this->morphMany('Modules\Facility\Models\FacilityServiceTiming', 'model');
    }

    /**
     * Get the Facilitys's timing.
     */
    public function facilityslot()
    {
        return $this->morphMany('Modules\Facility\Models\FacilityServiceSlot', 'model');
    }

    public function getFacility(){
        return $this->belongsTo('Modules\Facility\Models\Facility','facility_id','id');
    }

    public function getFacilitytrashed(){
        return $this->belongsTo('Modules\Facility\Models\Facility','facility_id','id')->withTrashed();
    }

    public function facility(){
        return $this->belongsTo('Modules\Facility\Models\Facility','facility_id','id');
    }
}
