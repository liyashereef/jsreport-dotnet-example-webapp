<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserCourseCertification extends Model
{
    use SoftDeletes;
    protected $table='osgc_user_course_certification';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id','course_id','certificate_name'];
   
}
