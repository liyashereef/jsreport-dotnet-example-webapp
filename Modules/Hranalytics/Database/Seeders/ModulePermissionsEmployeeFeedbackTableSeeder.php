<?php

namespace Modules\Hranalytics\Database\Seeders;

use App\Models\CustomPermission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionsEmployeeFeedbackTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $show_employee_feedback_inapp = HelperService::getPermissionId('show_employee_feedback_inapp');
        $view_allocated_sites_in_employeefeedback = HelperService::getPermissionId('view_allocated_sites_in_employeefeedback');
        $view_all_sites_in_employeefeedback = HelperService::getPermissionId('view_all_sites_in_employeefeedback');
        $view_transaction_department_allocation = HelperService::getPermissionId('view_transaction_department_allocation');
        $update_employee_feedback_status = HelperService::getPermissionId('update_employee_feedback_status');


        $module_id = \App\Services\HelperService::getModuleId('HR Analytics');
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'Show Employee Feedback in App',
                'created_at' => \Carbon::now(),
                'permission_id' => $show_employee_feedback_inapp,
                'sequence_number' => 273,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Sites In Employee feedback',
                'created_at' => \Carbon::now(),
                'permission_id' => $view_allocated_sites_in_employeefeedback,
                'sequence_number' => 274,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View All Sites In Employee feedback',
                'created_at' => \Carbon::now(),
                'permission_id' => $view_all_sites_in_employeefeedback,
                'sequence_number' => 275,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View Transactions With Department Allocation',
                'created_at' => \Carbon::now(),
                'permission_id' => $view_transaction_department_allocation,
                'sequence_number' => 276,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'Update Status In Dashboard',
                'created_at' => \Carbon::now(),
                'permission_id' => $update_employee_feedback_status,
                'sequence_number' => 278,
            ]
        ]);
    }

    public function getPermissionId($permission_name)
    {
        $permission_id = CustomPermission::where('name', $permission_name)->value('id');
        return $permission_id;
    }
}
