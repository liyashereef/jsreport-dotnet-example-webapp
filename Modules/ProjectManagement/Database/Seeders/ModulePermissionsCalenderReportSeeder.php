<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsCalenderReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rate_project_management_task_id = HelperService::getPermissionId('rate_project_management_task');
        $module_id = \App\Services\HelperService::getModuleId('Project Management');
        \DB::table('module_permissions')->where('module_id', $module_id)
                                        ->whereIn('permission_id', [$rate_project_management_task_id])
                                        ->delete();
        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Rate Task',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' => $rate_project_management_task_id,
                'sequence_number' => 110,
            ),


        ));
    }
}
