<?php

namespace Modules\UniformScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionsUniformSchedulingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module_id = HelperService::getModuleId('Uniform Scheduling');

        $all_appointment = HelperService::getPermissionId('uniform_view_all_appointment');
        $reschedule_appointment = HelperService::getPermissionId('uniform_reschedule_appointment');
        $booking_cancel = HelperService::getPermissionId('uniform_booking_cancel');
        $booking_delete = HelperService::getPermissionId('uniform_booking_delete');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Schedule Appointments',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'permission_id' => $all_appointment,
                'sequence_number' => 290,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'Reschedule Appointment',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'permission_id' => $reschedule_appointment,
                'sequence_number' => 291,
            ),
            2 => array(
                'module_id' => $module_id,
                'permission_description' => 'Delete Appointment',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'permission_id' => $booking_cancel,
                'sequence_number' => 292,
            ),
            3 => array(
                'module_id' => $module_id,
                'permission_description' => 'Cancel Appointment',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'permission_id' => $booking_delete,
                'sequence_number' => 293,
            ),
        ));

    }
}
