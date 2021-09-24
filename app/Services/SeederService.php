<?php

namespace App\Services;
use Spatie\Permission\Models\Permission;
use App\Models\ModulePermission;

class SeederService
{
    /**
     * Seed permissions that doesn't exists in db.
     * 
     * @param Array $permissions
     */
    public static function seedPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            $hasPermission = Permission::where('name', '=', $permission)->first();
            if (!$hasPermission) {
                Permission::create(['name' => $permission]);
            }
        }

    }

    /**
     * Attach permissions to a module.
     * 
     * @param Array $modulePermissions
     */
    public static function seedModulePermissions($modulePermissions)
    {
         //create module permission if not exists.
         foreach ($modulePermissions as $modulePermission) {
            $hasPermission = ModulePermission::where(
                'permission_id',
                '=',
                $modulePermission['permission_id']
            )->first();
            
            if (!$hasPermission) {
                ModulePermission::create($modulePermission);
            }
        }
    }

    public static function deletePermission($permissionNameArr) {
        $permissionsToDelete = \DB::table('permissions')
            ->whereIn('name', $permissionNameArr)->pluck('id');

        if (count($permissionsToDelete) > 0) {
            \DB::table('module_permissions')
                ->whereIn('permission_id', $permissionsToDelete)
                ->delete();

            \DB::table('permissions')
                ->whereIn('id', $permissionsToDelete)
                ->delete();
        }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }
}
