<?php

namespace Modules\Sensors\Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SensorsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SensorsModuleTableSeeder::class);
        $this->call(RolesPermissionMotionSensorViewTableSeeder::class);
        $this->call(ModulePermissionMotionSensorViewTableSeeder::class);
        $this->call(RolesAndPermissionsSensorsAdminTableSeeder::class);
        $this->call(ModulePermissionsSensorsAdminSeederTableSeeder::class);
        $this->call(RolesPermissionsMotionSensorLoginTableSeeder::class);
        $this->call(ModulePermissionsMotionSensorLoginTableSeeder::class);
    }
}
