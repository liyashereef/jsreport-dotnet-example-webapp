<?php

namespace Modules\LearningAndTraining\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['name', 'description','parent_team_id'];

    public function team(){
        return $this->belongsTo('Modules\LearningAndTraining\Models\Team', 'parent_team_id', 'id');
    }

    public function subs(){
        return $this->hasMany('Modules\LearningAndTraining\Models\Team', 'parent_team_id', 'id');//->select('id','name as title');
    }

    public function mandatory_course(){
        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation', 'team_id', 'id')->where('mandatory',1);
    }

    public function recommended_course(){
        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation', 'team_id', 'id')->where('recommended',1);
    }

    public function team_course(){
        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation', 'team_id', 'id');
    }

}
