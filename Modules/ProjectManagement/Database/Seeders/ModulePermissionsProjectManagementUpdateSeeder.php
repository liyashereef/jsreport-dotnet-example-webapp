<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsProjectManagementUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $update_report_id = HelperService::getPermissionId('update_report');
        $module_id = \App\Services\HelperService::getModuleId('Project Management');
        \DB::table('module_permissions')->where('module_id', $module_id)
                                        ->where('permission_id', $update_report_id)
                                        ->delete();
        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Update Report',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' => $update_report_id,
                'sequence_number' => 101,
            ),

        ));
    }
}
