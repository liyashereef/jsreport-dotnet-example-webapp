<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;

class ModulePermissionStatusApprovalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_allocated_post_order_id = HelperService::getPermissionId('view_allocated_post_order');
        $approve_postorder_id = HelperService::getPermissionId('approve_postorder');
        $approve_rfp_catalog_id = HelperService::getPermissionId('approve_rfp_catalog');
        $module_id = \App\Services\HelperService::getModuleId('Contracts');

        \DB::table('module_permissions')->where('module_id', $module_id)->whereIn('permission_id', [$view_allocated_post_order_id,$approve_postorder_id,$approve_rfp_catalog_id])->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Post Order',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $view_allocated_post_order_id,
                'sequence_number' => 107,
            ),
             1 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Approve Post Order',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $approve_postorder_id,
                'sequence_number' => 108,
            ),
            
                 2 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Approve RFP Catalog',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $approve_rfp_catalog_id,
                'sequence_number' => 111,
            ),
        ));
    }
}
