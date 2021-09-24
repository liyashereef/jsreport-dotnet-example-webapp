<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsCalenderReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'rate_project_management_task']);
    

        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
    }
}
