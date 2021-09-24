<?php

namespace Modules\IpCamera\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulesPermissionIpCameraTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission_id = HelperService::getPermissionId('view_ipcamera');
        $module_id     = HelperService::getModuleId('Ip Camera');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Ip Camera',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $permission_id,
                'sequence_number' => 1,
            ),
        ));

    }
}




