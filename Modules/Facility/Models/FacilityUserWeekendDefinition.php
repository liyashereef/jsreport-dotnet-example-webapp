<?php

namespace Modules\Facility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FacilityUserWeekendDefinition extends Model
{
    use SoftDeletes;
    protected $fillable = ['facility_service_user_allocation_id','day_id','created_by','updated_by'];

    
}
