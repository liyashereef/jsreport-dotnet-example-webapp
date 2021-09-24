<?php

namespace Modules\Sensors\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsSensorsAdminSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sensor_admin = HelperService::getPermissionId('sensors_admin');
        $module_id = HelperService::getModuleId('Admin');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Sensor Admin',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $sensor_admin,
                'sequence_number' => 116,
            )
        ));
    }
}
