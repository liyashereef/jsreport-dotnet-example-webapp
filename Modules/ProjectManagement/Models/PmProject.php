<?php

namespace Modules\ProjectManagement\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PmProject extends Model
{

	use SoftDeletes;
    public $timestamps = true; 
   
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','customer_id'];
    
    /**
     * The customer details that belongs to project management
     *
     */
     public function customerDetails()
     {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }
     /**
     * The customer details that belongs to project management
     *
     */
     public function groups()
     {
        return $this->hasMany('Modules\ProjectManagement\Models\PmGroup', 'project_id', 'id');
    }

      /**
     * The customer details that belongs to project management
     *
     */
     public function tasks()
     {
        return $this->hasMany('Modules\ProjectManagement\Models\PmTask', 'project_id', 'id')->whereDoesntHave('groupDetails');
    }

    public function taskList() 
     { 
        return $this->hasMany('Modules\ProjectManagement\Models\PmTask', 'project_id', 'id'); 

    } 
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
    return $query->with([$relation => $constraint])->whereHas($relation, $constraint);
    }
}
