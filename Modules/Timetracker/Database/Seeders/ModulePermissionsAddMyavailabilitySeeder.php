<?php

namespace Modules\Timetracker\Database\Seeders;

use App\Services\HelperService;
use Illuminate\Database\Seeder;

class ModulePermissionsAddMyavailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $add_my_availability_id = HelperService::getPermissionId('add_my_availability');
        $module_id = HelperService::getModuleId('Time Tracker');

        \DB::table('module_permissions')->insert(array(

            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Add Availability',
                'created_at' => '2019-11-20 02:13:41',
                'updated_at' => '2019-11-20 02:13:41',
                'permission_id' => $add_my_availability_id,
                'sequence_number' => 100,
            ),
        ));
    }
}
