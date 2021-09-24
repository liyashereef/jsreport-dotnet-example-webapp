<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProfileCourse extends Model
{

    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = ['training_profile_id', 'course_id', 'course_type', 'profile_type'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to training course table
     * @return type
     */
    public function training_course()
    {
        return $this->belongsTo('Modules\Admin\Models\TrainingCourse', 'course_id', 'id');
    }

    /**
     * Relation to training profile table
     * @return type
     */
    public function training_profile()
    {
        return $this->belongsTo('Modules\Admin\Models\TrainingProfile', 'training_profile_id', 'id');
    }

    public function training_siteprofile()
    {
        return $this->belongsTo('Modules\Admin\Models\TrainingProfileSite', 'training_profile_id', 'id');
    }
}
