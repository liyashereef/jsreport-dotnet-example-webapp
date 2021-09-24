<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseContent extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['content_type_id','value','course_id','fast_forward','content_title','content_order'];

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    /**
     * Relation to training category table
     * @return type
     */
    public function training_courses()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TrainingCourse', 'course_id', 'id');
    }
    public function course_content_types()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\CourseContentType', 'content_type_id', 'id');
    }
}
