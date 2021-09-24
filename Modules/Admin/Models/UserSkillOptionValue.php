<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class UserSkillOptionValue extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','user_skill_option_id','order','created_by','updated_by'];
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
    public function userSkillOption()
    {
        return $this->belongsTo('Modules\Admin\Models\UserSkillOption', 'user_skill_option_id', 'id');
    }
}
