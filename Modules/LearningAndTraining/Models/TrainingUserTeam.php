<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TrainingUserTeam extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['user_id', 'team_id','training_user_id'];

    public function team()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\Team', 'team_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }
}
