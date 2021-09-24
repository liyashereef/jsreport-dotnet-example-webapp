<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TestUserResult extends Model
{
    use SoftDeletes;
    
    protected $table = 'test_user_results';
    protected $fillable = ['training_course_id', 'user_id', 'test_course_master_id','course_pass_percentage','total_questions',
    'total_attempted_questions','total_exam_score','is_exam_pass','score_percentage','status','submitted_at','training_user_id'];
   
   
    public function TestUserAttemptedQuestion()
    {
        return $this->hasOne('Modules\LearningAndTraining\Models\TestUserAttemptedQuestion', 'test_user_result_id', 'id');
    }

    public function TestCourseMaster()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TestCourseMaster', 'test_course_master_id', 'id');
    }

    public function TrainingCourse()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TrainingCourse', 'training_course_id', 'id');
    }

    public function User()
    {
         return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }
    public function TrainingUser()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TrainingUser', 'training_user_id', 'id');
         //return $this->belongsTo('Modules\Admin\Models\User', 'training_user_id', 'id');
    }
}
