<?php

namespace Modules\Osgc\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\SeederService;
class RoleAndPermissionsOsgcRegisteredUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newPermissions = array(
            'view_osgc',
            'view_osgc_registered_users'
        );
        SeederService::seedPermissions($newPermissions);
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
