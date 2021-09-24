<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AllocatedUserCourses extends Model
{
    use SoftDeletes;
    protected $table='osgc_allocated_user_courses';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id','course_id','status','completed_time','course_section_id'];
    public function courseSection()
    {
        return $this->belongsTo(OsgcCourseContentSection::class,'course_section_id','id');

    }
}
