<?php

namespace Modules\Timetracker\Database\Seeders;

use App\Services\HelperService;
use Illuminate\Database\Seeder;

class ModulePermissionsTimeTrackerMSTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $view_mst_permission_id = HelperService::getPermissionId('view_mst');
        $view_all_mst_permission_id = HelperService::getPermissionId('view_all_mst');
        $view_dispatch_request_mst_permission_id = HelperService::getPermissionId('view_dispatch_request_mst');
        $module_id = HelperService::getModuleId('Time Tracker');

        \DB::table('module_permissions')->insert(array(
//            0 => array(
//                'module_id' => $module_id,
//                'permission_description' => 'View Allocated Users MST Record',
//                'created_at' => '2019-07-17 02:13:41',
//                'updated_at' => '2019-07-17 02:13:41',
//                'permission_id' => $view_mst_permission_id,
//                'sequence_number' => 67,
//            ),
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Users MST Record',
                'created_at' => '2019-07-17 02:13:41',
                'updated_at' => '2019-07-17 02:13:41',
                'permission_id' => $view_all_mst_permission_id,
                'sequence_number' => 67,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'Dispatch Request Management',
                'created_at' => '2019-07-17 02:13:41',
                'updated_at' => '2019-07-17 02:13:41',
                'permission_id' => $view_dispatch_request_mst_permission_id,
                'sequence_number' => 67,
            ),
        ));
    }
}
