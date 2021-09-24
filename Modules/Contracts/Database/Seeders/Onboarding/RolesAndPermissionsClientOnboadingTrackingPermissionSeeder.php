<?php

namespace Modules\Contracts\Database\Seeders\Onboarding;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsClientOnboadingTrackingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create permissions
        Permission::create(['name' => 'configure_client_onboarding_tracking']);
        Permission::create(['name' => 'view_assigned_client_onboarding_steps']);
        Permission::create(['name' => 'view_all_client_onboarding_steps']);
        Permission::create(['name' => 'update_client_onboarding_step_status']);


        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
