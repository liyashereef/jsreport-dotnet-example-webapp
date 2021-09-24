<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class RolesAndPermissionsGeomappingFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'view_dob_in_employee_geomapping']);
        Permission::create(['name' => 'view_age_in_employee_geomapping']);
        Permission::create(['name' => 'view_veteran_status_in_employee_geomapping']);
        Permission::create(['name' => 'view_employee_rating_in_employee_geomapping']);
        Permission::create(['name' => 'view_clearance_type_in_employee_geomapping']);
        Permission::create(['name' => 'view_candidate_score_in_candidate_geomapping']);
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
