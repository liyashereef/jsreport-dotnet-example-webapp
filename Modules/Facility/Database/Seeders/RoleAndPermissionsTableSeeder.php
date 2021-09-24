<?php

namespace Modules\Facility\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Permission::create(['name' => 'view_facilitybooking']);
        Permission::create(['name' => 'view_all_customer_facility']);
        Permission::create(['name' => 'view_allocated_customer_facility']);
        Permission::create(['name' => 'manage_all_customer_facility']);
        Permission::create(['name' => 'manage_allocated_customer_facility']);
        Permission::create(['name' => 'remove_customer_facility']);
        Permission::create(['name' => 'manage_all_customer_facility_service']);
        Permission::create(['name' => 'remove_customer_facility_service']);
        Permission::create(['name' => 'manage_all_facility_users']);
        Permission::create(['name' => 'manage_allocated_facility_users']);
        Permission::create(['name' => 'remove_facility_users']);
        Permission::create(['name' => 'manage_user_allocation']);

        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
