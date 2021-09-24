<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingCourse extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['reference_code', 'training_category_id', 'course_title',
    'course_description', 'course_objectives', 'course_due_date', 'add_to_course_library',
    'image_name', 'course_file', 'course_external_url', 'status','course_duration'];

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
    public function training_category()
    {
        return $this->belongsTo('Modules\Admin\Models\TrainingCategory', 'training_category_id', 'id');
    }

    public function TrainingUserCourseAllocation()
    {
        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingUserCourseAllocation', 'course_id', 'id');
    }
    public function CourseAllocationCount()
    {
        return $this->hasOne('Modules\LearningAndTraining\Models\TrainingUserCourseAllocation', 'course_id', 'id')
            ->selectRaw('course_id , count(*) as data_count')
            ->groupBy('course_id');
    }
    public function CourseUserAllocationCount()
    {
        return $this->hasOne('Modules\LearningAndTraining\Models\TrainingUserCourseAllocation', 'course_id', 'id')
        ->whereNotNull('user_id')
            ->selectRaw('course_id , count(*) as data_count')
            ->groupBy('course_id');
    }
    public function CourseTrainingUserAllocationCount()
    {
        return $this->hasOne('Modules\LearningAndTraining\Models\TrainingUserCourseAllocation', 'course_id', 'id')
        ->whereNotNull('training_user_id')
            ->selectRaw('course_id , count(*) as data_count')
            ->groupBy('course_id');
    }
    public function CourseAllocationCompletedCount()
    {
        return $this->hasOne('Modules\LearningAndTraining\Models\TrainingUserCourseAllocation', 'course_id', 'id')
            ->selectRaw('course_id , count(*) as data_count')
            ->where('completed', 1)
            ->groupBy('course_id');
    }
    public function CourseUserAllocationCompletedCount()
    {
        return $this->hasOne('Modules\LearningAndTraining\Models\TrainingUserCourseAllocation', 'course_id', 'id')
           ->whereNotNull('user_id')
           ->selectRaw('course_id , count(*) as data_count')
           ->where('completed', 1)
           ->groupBy('course_id');
    }
    public function CourseTrainingUserAllocationCompletedCount()
    {
        return $this->hasOne('Modules\LearningAndTraining\Models\TrainingUserCourseAllocation', 'course_id', 'id')
           ->whereNotNull('training_user_id')
           ->selectRaw('course_id , count(*) as data_count')
           ->where('completed', 1)
           ->groupBy('course_id');
    }

    public function TestUserSuccessResult()
    {
        return $this->hasOne('Modules\LearningAndTraining\Models\TestUserResult', 'training_course_id', 'id')->latest();
    }
}
