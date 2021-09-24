<?php

namespace Modules\Facility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityUserPrerequisiteAnswer extends Model
{
    use SoftDeletes;
    protected $fillable = ['facility_id','facility_allocation_id','user_id','prereq_id','answer','created_by','updated_by'];
}
