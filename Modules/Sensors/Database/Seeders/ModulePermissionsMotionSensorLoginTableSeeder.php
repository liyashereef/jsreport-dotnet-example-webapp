<?php

namespace Modules\Sensors\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsMotionSensorLoginTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $motion_sensor_login = HelperService::getPermissionId('motion_sensor_login');
        $module_id = HelperService::getModuleId('Sensors');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Motion Sensor Login',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' => $motion_sensor_login,
                'sequence_number' => 104,
            )
        ));
    }
}
