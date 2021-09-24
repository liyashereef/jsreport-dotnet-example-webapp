<?php

namespace Modules\Facility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityPolicy extends Model
{
    use SoftDeletes;
    protected $fillable = ['facility_id','policy','order','created_by','updated_by'];
}
