<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsCustomerAllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('permissions')->whereIn('name', [
            'rec-view-allocated-job-requisitions',
            'rec-view-allocated-candidates-summary',
            'rec-view-allocated-candidates-geomapping',
            'rec-view-allocated-candidates-tracking',
            ])->delete();

        Permission::create(['name' => 'rec-view-allocated-job-requisitions']);
        Permission::create(['name' => 'rec-view-allocated-candidates-summary']);
        Permission::create(['name' => 'rec-view-allocated-candidates-geomapping']);
        Permission::create(['name' => 'rec-view-allocated-candidates-tracking']);
       

        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
