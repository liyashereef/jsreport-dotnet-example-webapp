<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeSurveyPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'view_all_employee_surveys']);
        Permission::create(['name' => 'view_allocated_employee_surveys']);
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());

        $view_all_employee_surveys = \App\Services\HelperService::getPermissionId('view_all_employee_surveys');
        $view_allocated_employee_surveys = \App\Services\HelperService::getPermissionId('view_allocated_employee_surveys');
        $module_id = \App\Services\HelperService::getModuleId('HR Analytics');
        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Employee Survey',
                'created_at' => '2020-10-15 06:51:55',
                'updated_at' => '2020-10-15 06:51:55',
                'permission_id' => $view_all_employee_surveys,
                'sequence_number' => 255,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Employee Survey',
                'created_at' => '2020-10-15 06:51:55',
                'updated_at' => '2020-10-15 06:51:55',
                'permission_id' => $view_allocated_employee_surveys,
                'sequence_number' => 256,
            )
        ));
    }
}
