<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingTeamCourseAllocation extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['course_id','team_id','parent_team_id','mandatory','recommended'];

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
     * Relation to training course table
     * @return type
     */
    public function team()
    {
        return $this->belongsTo('Modules\Admin\Models\Team', 'team_id', 'id');
    }

}
