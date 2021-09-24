<?php

namespace Modules\IdsScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionsCancelAndDeleteOfIDSSchedulingTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $module_id = HelperService::getModuleId('IDS Scheduling');
        $ids_booking_delete = HelperService::getPermissionId('ids_booking_delete');
        $ids_booking_cancel = HelperService::getPermissionId('ids_booking_cancel');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Delete Appointment',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'permission_id' => $ids_booking_delete,
                'sequence_number' => 128,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'Cancel Appointment',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'permission_id' => $ids_booking_cancel,
                'sequence_number' => 129,
            )
        ));

    }
}
