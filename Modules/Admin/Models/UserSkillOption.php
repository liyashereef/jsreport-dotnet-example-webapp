<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class UserSkillOption extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','created_by','updated_by'];
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
    public function skillOptionValues()
    {
        return $this->hasMany('Modules\Admin\Models\UserSkillOptionValue', 'user_skill_option_id', 'id')->orderBy('order');
    }
}
