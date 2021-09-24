<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserCourseCompletion extends Model
{
    use SoftDeletes;
    protected $table='osgc_user_course_completion';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id','course_section_id','course_header_id','test_started','test_completed','content_started','content_completed','status'];
    public function course_section()
    {
        return $this->belongsTo('Modules\Osgc\Models\OsgcCourseContentSection', 'course_section_id', 'id');
    }
}
