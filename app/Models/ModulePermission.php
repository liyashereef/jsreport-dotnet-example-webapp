<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{

    public $timestamps = true;
    protected $fillable = ['module_id', 'permission_id', 'permission_description','status'];

    /**
     * Relation to Modules
     *
     * @return type
     */
    public function module_permission()
    {
        return $this->belongsTo('App\Models\Modules', 'module_id', 'id');
    }

    /**
     * Relation to  Permissions
     *
     * @return type
     */
    public function permission()
    {
        return $this->belongsTo('App\Models\CustomPermission', 'permission_id', 'id');
    }

}
