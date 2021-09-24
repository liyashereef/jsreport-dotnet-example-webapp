<?php

namespace Modules\Osgc\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;
use App\Services\SeederService;
class ModulePermissionsOsgcRegisteredUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission_id = HelperService::getPermissionId('view_osgc_registered_users');
        $module_id     = HelperService::getModuleId('Osgc');

        $modulePermissionArr = [
            array(
                'module_id' => $module_id,
                'permission_description' => 'View Osgc Registered Users',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'permission_id' => $permission_id,
                'sequence_number' => 1,
            )
        ];
        SeederService::seedModulePermissions($modulePermissionArr);
        
    }
}
