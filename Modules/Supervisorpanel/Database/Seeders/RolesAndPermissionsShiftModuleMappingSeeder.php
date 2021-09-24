<?php

namespace Modules\Supervisorpanel\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsShiftModuleMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'view_all_shift_module_mapping']);
        Permission::create(['name' => 'view_allocated_shift_module_mapping']);

        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
    }
}
