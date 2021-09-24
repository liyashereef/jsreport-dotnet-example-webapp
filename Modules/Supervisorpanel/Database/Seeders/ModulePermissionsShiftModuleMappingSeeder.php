<?php

namespace Modules\Supervisorpanel\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsShiftModuleMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_all_shift_module_mapping = HelperService::getPermissionId('view_all_shift_module_mapping');
        $view_allocated_shift_module_mapping = HelperService::getPermissionId('view_allocated_shift_module_mapping');
        $module_id = \App\Services\HelperService::getModuleId('Supervisor Panel');
        \DB::table('module_permissions')->where('module_id', $module_id)
                                        ->whereIn('permission_id', [$view_all_shift_module_mapping,$view_allocated_shift_module_mapping])
                                        ->delete();
        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View All Shift Module Mapping',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_all_shift_module_mapping,
                'sequence_number' => 288,
            ),
                1=> array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Shift Module Mapping',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_allocated_shift_module_mapping,
                'sequence_number' => 289,
            )

        ));
    }
}
