<?php

namespace Modules\IdsScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use App\Services\SeederService;

class ModulePermissionsOfRefundTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module_id = HelperService::getModuleId('IDS Scheduling');
        $ids_refund_list = HelperService::getPermissionId('ids_refund_list');
        $ids_refund_update_status = HelperService::getPermissionId('ids_refund_update_status');

        $modulePermissionArr = [
            array(
                'module_id' => $module_id,
                'permission_description' => 'View Refund List',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'permission_id' => $ids_refund_list,
                'sequence_number' => 131,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'Manage Refund Status',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'permission_id' => $ids_refund_update_status,
                'sequence_number' => 132,
            )
        ];
        SeederService::seedModulePermissions($modulePermissionArr);
    }
}
