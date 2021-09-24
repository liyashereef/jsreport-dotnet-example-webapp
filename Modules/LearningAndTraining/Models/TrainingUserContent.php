<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingUserContent extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['user_id', 'course_content_id', 'completed','completed_length','completed_percentage','completed_date','training_user_id'];

    public function user()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\User', 'user_id', 'id');
    }

    public function course_content()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\CourseContent', 'course_content_id', 'id');
    }
}
