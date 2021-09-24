<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OsgcCourseContentSection extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['name','sort_order','course_id','header_id','completion_mandatory','active'];
    public function courseContent()
    {
        return $this->hasOne('Modules\Osgc\Models\OsgcCourseContent', 'course_content_section_id', 'id');

    }
    public function courseHeading()
    {
        return $this->belongsTo(OsgcCourseContentHeader::class,'header_id','id');

    }
    public function courseUserCompletion()
    {
        return $this->hasOne('Modules\Osgc\Models\UserCourseCompletion', 'course_section_id', 'id')->where('user_id',\Auth::user()->id);

    }
    public function studyGuide()
    {
        return $this->hasOne('Modules\Osgc\Models\CourseStudyGuide', 'course_section_id', 'id');

    }
}
