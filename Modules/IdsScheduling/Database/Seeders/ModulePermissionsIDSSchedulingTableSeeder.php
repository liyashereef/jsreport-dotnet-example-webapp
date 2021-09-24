<?php

namespace Modules\IdsScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionsIDSSchedulingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $module_id = \App\Services\HelperService::getModuleId('IDS Scheduling');

        $ids_view_all_schedule = HelperService::getPermissionId('ids_view_all_schedule');
        $ids_view_allocated_locaion_schedule = HelperService::getPermissionId('ids_view_allocated_locaion_schedule');
        $ids_reschedule_appointment = HelperService::getPermissionId('ids_reschedule_appointment');
        $ids_view_report = HelperService::getPermissionId('ids_view_report');
        // $ids_allocate_location = HelperService::getPermissionId('ids_allocate_location');
        $reschedule_request = HelperService::getPermissionId('reschedule_request');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Schedule',
                'created_at' => '2019-06-10 01:35:55',
                'updated_at' => '2019-06-10 01:35:55',
                'permission_id' => $ids_view_all_schedule,
                'sequence_number' => 124,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Location Schedule',
                'created_at' => '2019-06-10 01:35:55',
                'updated_at' => '2019-06-10 01:35:55',
                'permission_id' => $ids_view_allocated_locaion_schedule,
                'sequence_number' => 125,
            ),
            2 => array(
                'module_id' => $module_id,
                'permission_description' => 'Reschedule Appointment',
                'created_at' => '2019-06-10 01:35:55',
                'updated_at' => '2019-06-10 01:35:55',
                'permission_id' => $ids_reschedule_appointment,
                'sequence_number' => 126,
            ),
            3 => array(
                'module_id' => $module_id,
                'permission_description' => 'Reschedule Request',
                'created_at' => '2019-06-10 01:35:55',
                'updated_at' => '2019-06-10 01:35:55',
                'permission_id' => $reschedule_request,
                'sequence_number' => 127,
            ),
            4 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Report',
                'created_at' => '2019-06-10 01:35:55',
                'updated_at' => '2019-06-10 01:35:55',
                'permission_id' => $ids_view_report,
                'sequence_number' => 130,
            ),
            // 5 => array(
            //     'module_id' => $module_id,
            //     'permission_description' => 'Allocate Location',
            //     'created_at' => '2019-06-10 01:35:55',
            //     'updated_at' => '2019-06-10 01:35:55',
            //     'permission_id' => $ids_allocate_location,
            // ),

        ));
    }
}
