<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OsgcCoursePrice extends Model
{
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['price','created_by','updated_by','course_id'];
}
