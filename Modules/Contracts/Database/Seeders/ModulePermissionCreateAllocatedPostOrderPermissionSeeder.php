<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;

class ModulePermissionCreateAllocatedPostOrderPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $create_allocated_post_order_id = HelperService::getPermissionId('create_allocated_post_order');
        $module_id = \App\Services\HelperService::getModuleId('Contracts');

        \DB::table('module_permissions')->where('module_id', $module_id)->whereIn('permission_id', [$create_allocated_post_order_id])->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Create Allocated Post Order',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $create_allocated_post_order_id,
                'sequence_number' => 106,
            ),
        
        ));
    }
}
