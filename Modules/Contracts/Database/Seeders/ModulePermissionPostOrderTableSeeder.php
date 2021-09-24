<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;

class ModulePermissionPostOrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_post_order_id = HelperService::getPermissionId('view_post_order');
        $create_post_order_id = HelperService::getPermissionId('create_post_order');
        $module_id = \App\Services\HelperService::getModuleId('Contracts');

        \DB::table('module_permissions')->where('module_id', $module_id)->whereIn('permission_id', [$view_post_order_id,$create_post_order_id])->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Create All Post Order',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $create_post_order_id,
                'sequence_number' => 105,
            ),
             1 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View All Post Order',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $view_post_order_id,
                'sequence_number' => 106,
            ),
        ));
    }
}
