<?php

namespace Modules\Facility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityPrerequisite extends Model
{
    use SoftDeletes;
    protected $fillable = ['facility_id','requisite','order','created_by','updated_by'];

    public function FacilityUserPrerequisiteAnswer(){
        return $this->hasMany('Modules\Facility\Models\FacilityUserPrerequisiteAnswer','prereq_id','id');
    }
}
