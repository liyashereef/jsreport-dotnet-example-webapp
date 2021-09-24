<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsEmployeeFeedbackTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Permission::create(['name' => 'show_employee_feedback_inapp']);
        Permission::create(['name' => 'view_allocated_sites_in_employeefeedback']);
        Permission::create(['name' => 'view_all_sites_in_employeefeedback']);
        Permission::create(['name' => 'view_transaction_department_allocation']);
        Permission::create(['name' => 'update_employee_feedback_status']);


        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
