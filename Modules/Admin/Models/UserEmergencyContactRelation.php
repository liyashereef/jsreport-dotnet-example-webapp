<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserEmergencyContactRelation extends Model
{
    use SoftDeletes;

    protected $fillable = ['relations','apogee_code','created_by','updated_by'];
    protected $dates = ['created_at','updated_at','deleted_at'];
}
