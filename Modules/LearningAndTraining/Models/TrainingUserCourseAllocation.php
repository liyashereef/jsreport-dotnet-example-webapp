<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingUserCourseAllocation extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['user_id','created_by','updated_by','course_id','mandatory','recommended','completed','completed_percentage','completed_date','manual_completion','manual_completion_date','training_user_id'];


    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }
    public function trashed_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }
    public function trainingUser()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TrainingUser', 'training_user_id', 'id');
    }

    public function created_by()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id');
    }

    public function updated_by()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'updated_by', 'id');
    }

    public function course()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TrainingCourse', 'course_id', 'id');
    }

    public function course_with_trashed()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TrainingCourse', 'course_id', 'id')->withTrashed();
    }

    public function trainingUserTeamCourseAllocation()
    {
        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingUserTeamCourseAllocation', 'training_user_course_allocation_id', 'id');
    }
    public function TrainingTeamCourseAllocation()
    {
        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation', 'course_id', 'course_id');
    }

    public function TestUserSuccessResult()
    {
        return $this->hasMany('Modules\LearningAndTraining\Models\TestUserResult', 'user_id', 'user_id')->orderBy('id', 'DESC');
    }

    public function customerEmployeeAllocation(){
        return $this->hasMany('Modules\Admin\Models\CustomerEmployeeAllocation', 'user_id', 'user_id');
    }

    public function TestTrainingUserSuccessResult()
    {
        return $this->hasMany('Modules\LearningAndTraining\Models\TestUserResult', 'training_user_id', 'training_user_id')->orderBy('id', 'DESC');
    }
}
