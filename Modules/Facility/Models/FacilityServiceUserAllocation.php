<?php

namespace Modules\Facility\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FacilityServiceUserAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ['facility_user_id','model_type','model_id','created_by','updated_by'];

    /**
     * Get the owning imageable model.
     */
    public function model()
    {
        return $this->morphTo();
    }

    public function facilityuserweekenddefinition()
    {
        return $this->hasMany('Modules\Facility\Models\FacilityUserWeekendDefinition','facility_service_user_allocation_id','id');
    }

    public function facilityuserprerequisiteanswer()
    {
        return $this->hasMany('Modules\Facility\Models\FacilityUserPrerequisiteAnswer','facility_allocation_id','id');
    }

    public function facility()
    {
        return $this->belongsTo('Modules\Facility\Models\Facility','model_id','id');;
    }
}
