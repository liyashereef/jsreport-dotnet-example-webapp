<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingUserTeamCourseAllocation extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['training_user_course_allocation_id', 'team_id'];

    public function team(){
        return $this->belongsTo('Modules\LearningAndTraining\Models\Team', 'team_id', 'id');
    }

    public function trainingUserCourseAllocation(){
        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingUserCourseAllocation', 'id', 'training_user_course_allocation_id');
    }

}
