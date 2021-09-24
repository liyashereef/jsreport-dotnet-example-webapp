<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingCourseUserRating extends Model
{
    public $timestamps = true;
    protected $fillable = ['user_id','course_id','rating','training_user_id'];
}
