<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TestCourseMaster extends Model
{
	use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['training_course_id','exam_name','number_of_question','random_question','pass_percentage','created_by','active'];

     /**
     * Relation to training category table
     * @return type
     */
    public function training_courses()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TrainingCourse', 'training_course_id', 'id');
    }

    
     public function test_questions()
    {
        return $this->hasMany('Modules\LearningAndTraining\Models\TestCourseQuestion', 'test_course_master_id', 'id');
    }

}
