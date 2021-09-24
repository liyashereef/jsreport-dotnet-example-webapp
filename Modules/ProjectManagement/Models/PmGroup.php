<?php

namespace Modules\ProjectManagement\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PmGroup extends Model
{

	use SoftDeletes;
    public $timestamps = true; 
   
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','project_id','is_default'];
    
    /**
     * The customer details that belongs to project management
     *
     */
     public function projectDetails()
     {
        return $this->belongsTo('Modules\ProjectManagement\Models\PmProject', 'project_id', 'id');
    }

  /**
     * The customer details that belongs to project management
     *
     */
     public function tasks()
     {
        return $this->hasMany('Modules\ProjectManagement\Models\PmTask', 'group_id', 'id');
    }
     public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
    return $query->with([$relation => $constraint])->whereHas($relation, $constraint);
    }
}