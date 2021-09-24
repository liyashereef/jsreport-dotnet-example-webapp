<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OsgcCourse extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['title','created_by','updated_by','active','description','course_image'];

    public function CourseHeaders()
    {
        return $this->hasMany('Modules\Osgc\Models\OsgcCourseContentHeader','course_id','id')->orderBy('sort_order','asc');
    }
    public function CourseSections()
    {
        return $this->hasMany('Modules\Osgc\Models\OsgcCourseContentSection','course_id','id')->orderBy('sort_order','asc');
    }
    public function CoursePrice()
    {
        return $this->hasOne('Modules\Osgc\Models\OsgcCoursePrice', 'course_id', 'id');
    }

    public function ActiveCourseHeaders()
    {
        return $this->hasMany('Modules\Osgc\Models\OsgcCourseContentHeader','course_id','id')->where('active',1)->orderBy('sort_order','asc');
    }
    public function ActiveCourseSections()
    {
        return $this->hasMany('Modules\Osgc\Models\OsgcCourseContentSection','course_id','id')->where('active',1)->orderBy('sort_order','asc');
    }
    public function CoursePayment()
    {
        return $this->hasOne('Modules\Osgc\Models\CoursePayment', 'course_id', 'id')->where('status',1)->where('user_id',\Auth::guard('osgcuser')->user()->id);
    }
}
