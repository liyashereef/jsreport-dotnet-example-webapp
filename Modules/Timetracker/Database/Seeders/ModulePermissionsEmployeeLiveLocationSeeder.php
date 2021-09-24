<?php

namespace Modules\Timetracker\Database\Seeders;

use App\Services\HelperService;
use Illuminate\Database\Seeder;

class ModulePermissionsEmployeeLiveLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employee_live_location_id = HelperService::getPermissionId('view_employee_live_location');
        $module_id = HelperService::getModuleId('Time Tracker');

        \DB::table('module_permissions')->insert(array(

            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Employee Live Location',
                'created_at' => '2019-11-20 02:13:41',
                'updated_at' => '2019-11-20 02:13:41',
                'permission_id' => $employee_live_location_id,
                'sequence_number' => 68,
            )
        ));
    }
}
