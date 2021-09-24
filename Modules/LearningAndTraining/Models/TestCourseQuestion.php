<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TestCourseQuestion extends Model
{
	 use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['test_course_master_id','test_question','is_mandatory_display','status'];

      public function test_question_options()
    {
        return $this->hasMany('Modules\LearningAndTraining\Models\TestCourseQuestionOption', 'test_course_question_id', 'id');
    }

     /**
     * Relation to training category table
     * @return type
     */
    public function test_course_masters()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TestCourseMaster', 'test_course_master_id', 'id');
    }
}
