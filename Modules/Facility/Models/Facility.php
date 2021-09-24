<?php

namespace Modules\Facility\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use SoftDeletes;
    protected $fillable = ['facility','description','single_service_facility','customer_id','restrict_booking','active','created_by','updated_by'];
    
    public function facilityservices()
    {
        return $this->hasMany('Modules\Facility\Models\FacilityService','facility_id','id');
    }
    /**
     * Get the Facilitys's data.
     */
    public function facilitydata()
    {
        return $this->morphOne('Modules\Facility\Models\FacilityServiceData', 'model');
    }

    public function facilityuserprerequisiteanswer()
    {
        return $this->hasMany('Modules\Facility\Models\FacilityUserPrerequisiteAnswer','facility_id','id');
    }

     /**
     * Get the Facilitys's data.
     */
    public function FacilityServiceDataMany()
    {
        return $this->morphOne('Modules\Facility\Models\FacilityServiceData', 'model');
    }
    /**
     * Get the Facilitys's allocation.
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

    public function facilityprerequisites(){
        return $this->hasMany('Modules\Facility\Models\FacilityPrerequisite','facility_id','id')->orderBy('order');
    }

    public function FacilityPolicy(){
        return $this->hasMany('Modules\Facility\Models\FacilityPolicy','facility_id','id')->orderBy('order');
    }

    public function facilityservicelockdown(){
        return $this->morphMany('Modules\Facility\Models\FacilityServiceLockdown', 'model');
    }

    public function customer(){
        return $this->belongsTo('Modules\Admin\Models\Customer','customer_id','id');
    }
    
}
