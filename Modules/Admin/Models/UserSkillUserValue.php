<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class UserSkillUserValue extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id','user_skill_option_allocation_id','user_option_value_id','created_by','updated_by'];
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $user = Auth::user();
            $model->created_by = $user->id;
            $model->updated_by = $user->id;
        });
        static::updating(function ($model) {
            $user = Auth::user();
            $model->updated_by = $user->id;
        });
    }
      /**
     * Relationship: user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id'); //
    }
    public function optionAllocation()
    {
        return $this->belongsTo('Modules\Admin\Models\UserSkillOptionAllocation', 'user_skill_option_allocation_id', 'id'); //
    }
    public function userOptionValue()
    {
        return $this->belongsTo('Modules\Admin\Models\UserSkillOptionValue', 'user_option_value_id', 'id'); //
    }
}
