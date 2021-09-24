<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerEmployeeAllocation extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'customer_id', 'created_by', 'updated_by', 'from', 'to'];

    /**
     * The user that belongs to employee allocation
     *
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

    /**
     * The customer that belongs to employee allocation
     *
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    /**
     * Users with trashed
     * @return type
     */
    public function trashedUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->with('roles')->withTrashed();
    }

    /**
     * Supervisor of a site by permission
     * @return type
     */
    public function supervisor()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')
        ->whereHas('roles.permissions' , function($query){
            $query->where('name','supervisor');
        });
    }

    /**
     * Area manager of a site by permission
     * @return type
     */
    public function areaManager()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')
        ->whereHas('roles.permissions' , function($query){
            $query->where('name','area_manager');});
    }

}
