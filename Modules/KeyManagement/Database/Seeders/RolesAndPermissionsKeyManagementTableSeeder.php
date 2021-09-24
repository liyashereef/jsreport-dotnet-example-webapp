<?php

namespace Modules\KeyManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsKeyManagementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create permissions
        // Module Vehicle
        Permission::create(['name' => 'view_allocated_customers_keys']);
        Permission::create(['name' => 'view_all_customers_keys']);
        Permission::create(['name' => 'add_edit_keys']);
        Permission::create(['name' => 'delete_keys']);
        Permission::create(['name' => 'view_all_keylog_summary']);
        Permission::create(['name' => 'view_keymanagement']);
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
