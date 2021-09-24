<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OsgcCourseContent extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['course_content_section_id','content_type_id','content','created_by','updated_by','fast_forward'];
    public function courseContentType()
    {
        return $this->hasOne('Modules\LearningAndTraining\Models\CourseContentType', 'id','content_type_id');

    }
   
}
