<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProfileRole extends Model
{

    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = ['training_profile_id', 'role_id'];

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
    public function training_profiles()
    {
        return $this->belongsTo('Modules\Admin\Models\TrainingProfile', 'training_profile_id', 'id');
    }

    /**
     * Relation to training profile table
     * @return type
     */
    public function role_name()
    {
        return $this->belongsTo('Spatie\Permission\Models\Role', 'role_id', 'id');
    }

}
