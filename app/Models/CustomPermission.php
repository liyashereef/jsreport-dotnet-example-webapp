<?php

namespace App\Models;

use Spatie\Permission\Models\Permission;

class CustomPermission extends Permission
{
    public $timestamps = true;
    protected $fillable = ['name', 'guard_name'];

    /**
     * Relation to Module Permissions
     *
     * @return type
     */
    public function module_permission()
    {
        return $this->hasOne('App\Models\ModulePermission', 'permission_id', 'id');
    }

}
