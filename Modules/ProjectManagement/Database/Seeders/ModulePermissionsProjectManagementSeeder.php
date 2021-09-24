<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsProjectManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_all_reports_id = HelperService::getPermissionId('view_all_reports');
        $view_allocated_customer_reports_id = HelperService::getPermissionId('view_allocated_customer_reports');
        $view_assigned_reports_id = HelperService::getPermissionId('view_assigned_reports');
        $create_task_all_customer_id = HelperService::getPermissionId('create_task_all_customer');
        $create_task_allocated_customer_id = HelperService::getPermissionId('create_task_allocated_customer');
        $module_id = \App\Services\HelperService::getModuleId('Project Management');
        \DB::table('module_permissions')->where('module_id', $module_id)
                                        ->whereIn('permission_id', [$view_all_reports_id,$view_allocated_customer_reports_id,$view_assigned_reports_id,$create_task_all_customer_id,$create_task_allocated_customer_id])
                                        ->delete();
        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View All Reports',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' => $view_all_reports_id,
                'sequence_number' => 102,
            ),
                1=> array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Reports',
                'created_at' => Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' => $view_allocated_customer_reports_id,
                'sequence_number' => 103,
            ),
                2 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Assigned Reports',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_assigned_reports_id,
                'sequence_number' => 104,
            ),
                3 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Create Task for All Customer Projects',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' => $create_task_all_customer_id,
                'sequence_number' => 105,
            ),
                4 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Create Task for Allocated Customer Projects',
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $create_task_allocated_customer_id,
                'sequence_number' => 106,
            ),

        ));
    }
}
