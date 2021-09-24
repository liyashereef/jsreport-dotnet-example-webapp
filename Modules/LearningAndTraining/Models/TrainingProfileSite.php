<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProfileSite extends Model
{

    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = ['profile_name', 'customer_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to cutomer table
     * @return type
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    public function training_site_profile()
    {
        return $this->hasOne('Modules\Admin\Models\TrainingProfile', 'training_profile_id', 'id');
    }
    public function training_profile_site_course()
    {
        return $this->hasMany('Modules\Admin\Models\TrainingProfileCourse', 'training_profile_id', 'id');
    }

}
