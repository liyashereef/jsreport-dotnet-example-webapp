<?php

namespace Modules\Sensors\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Carbon\Carbon;


class ModulePermissionMotionSensorViewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sensor_admin = HelperService::getPermissionId('motion_sensor_view');
        $module_id = HelperService::getModuleId('Sensors');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Motion Sensor',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $sensor_admin,
                'sequence_number' => 105,
            )
        ));

    }
}
