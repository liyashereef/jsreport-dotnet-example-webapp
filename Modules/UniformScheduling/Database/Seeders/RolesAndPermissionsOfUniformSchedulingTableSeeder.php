<?php

namespace Modules\UniformScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsOfUniformSchedulingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'view_uniformscheduling']);
        Permission::create(['name' => 'uniform_view_all_appointment']);
        Permission::create(['name' => 'uniform_reschedule_appointment']);
        Permission::create(['name' => 'uniform_booking_cancel']);
        Permission::create(['name' => 'uniform_booking_delete']);

        //Rewrite permission
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
