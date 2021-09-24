<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsProjectManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'view_projectmanagement']);
        Permission::create(['name' => 'view_all_reports']);
        Permission::create(['name' => 'view_allocated_customer_reports']);
        Permission::create(['name' => 'view_assigned_reports']);
        Permission::create(['name' => 'create_task_all_customer']);
        Permission::create(['name' => 'create_task_allocated_customer']);


        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        
    }
}