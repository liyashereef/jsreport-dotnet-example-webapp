<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class UserSkillOptionAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_skill_id','user_skill_option_id','created_by','updated_by'];
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
     * The skill that belongs to  allocation
     *
     */
    public function skill()
    {
        return $this->belongsTo('Modules\Admin\Models\UserSkill', 'user_skill_id', 'id');
    }
    /**
     * The skill Option that belongs to  allocation
     *
     */
    public function skillOption()
    {
        return $this->belongsTo('Modules\Admin\Models\UserSkillOption', 'user_skill_option_id', 'id');
    }
}
