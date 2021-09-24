<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProfile extends Model
{

    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = ['profile_name'];

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
    public function training_profile_role()
    {
        return $this->hasOne('Modules\Admin\Models\TrainingProfileRole', 'training_profile_id', 'id');
    }

    public function training_profile_course()
    {
        return $this->hasMany('Modules\Admin\Models\TrainingProfileCourse', 'training_profile_id', 'id');
    }

}
