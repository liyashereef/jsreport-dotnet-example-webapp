<?php

namespace Modules\IdsScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsOfIDSSchedulingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'view_idsscheduling']);
        Permission::create(['name' => 'ids_view_all_schedule']);
        Permission::create(['name' => 'ids_view_allocated_locaion_schedule']);
        Permission::create(['name' => 'ids_reschedule_appointment']);
        Permission::create(['name' => 'reschedule_request']);
        Permission::create(['name' => 'ids_view_report']);
        // Permission::create(['name' => 'ids_allocate_location']);
        
        //Rewrite permission
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
