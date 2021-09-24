<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsPerformanceReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_all_performance_reports_id = HelperService::getPermissionId('view_all_performance_reports');
        $view_allocated_performance_reports_id = HelperService::getPermissionId('view_allocated_performance_reports');
        $module_id = \App\Services\HelperService::getModuleId('Project Management');
        \DB::table('module_permissions')->where('module_id', $module_id)
                                        ->whereIn('permission_id', [$view_all_performance_reports_id,$view_allocated_performance_reports_id])
                                        ->delete();
        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View All Performance Reports',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_all_performance_reports_id,
                'sequence_number' => 108,
            ),
                1=> array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Performance Reports',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_allocated_performance_reports_id,
                'sequence_number' => 109,
            )

        ));
    }
}
