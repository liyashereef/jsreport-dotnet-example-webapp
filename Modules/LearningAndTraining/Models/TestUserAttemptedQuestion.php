<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TestUserAttemptedQuestion extends Model
{
    use SoftDeletes;
    protected $table = 'test_user_attempted_questions';
    protected $fillable = ['test_user_result_id','test_course_question_id','test_course_question_option_id','is_correct_answer'];

    public function TestUserResult(){
        return $this->belongsTo('Modules\LearningAndTraining\Models\TestUserResult', 'training_course_id', 'id');
     }
     
     public function TestCourseQuestion(){
        return $this->belongsTo('Modules\LearningAndTraining\Models\TestCourseQuestion', 'test_course_question_id', 'id');
     }
  
}
