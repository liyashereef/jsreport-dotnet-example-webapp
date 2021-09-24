<?php

namespace Modules\KeyManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsKeyManagementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_allocated_customers_keys = \App\Services\HelperService::getPermissionId('view_allocated_customers_keys');
        $view_all_customers_keys = \App\Services\HelperService::getPermissionId('view_all_customers_keys');
        $add_edit_keys = \App\Services\HelperService::getPermissionId('add_edit_keys');
        $delete_keys = \App\Services\HelperService::getPermissionId('delete_keys');
        $view_all_keylog_summary = \App\Services\HelperService::getPermissionId('view_all_keylog_summary');
        $module_id = \App\Services\HelperService::getModuleId('Key Management');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View allocated customers and keys',
                'created_at' => '2020-07-04 06:51:55',
                'updated_at' => '2020-07-04 06:51:55',
                'permission_id' => $view_allocated_customers_keys,
                'sequence_number' => 1,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'View all customers and keys',
                'created_at' => '2020-07-04 06:51:55',
                'updated_at' => '2020-07-04 06:51:55',
                'permission_id' => $view_all_customers_keys,
                'sequence_number' => 2,
            ),
            2 => array(
                'module_id' => $module_id,
                'permission_description' => 'Add/Edit key',
                'created_at' => '2020-07-04 06:51:55',
                'updated_at' => '2020-07-04 06:51:55',
                'permission_id' => $add_edit_keys,
                'sequence_number' => 3,
            ),
            3 => array(
                'module_id' => $module_id,
                'permission_description' => 'Delete key',
                'created_at' => '2020-07-04 06:51:55',
                'updated_at' => '2020-07-04 06:51:55',
                'permission_id' => $delete_keys,
                'sequence_number' => 4,
            ),
            4 => array(
                'module_id' => $module_id,
                'permission_description' => 'View all key log summary',
                'created_at' => '2020-07-04 06:51:55',
                'updated_at' => '2020-07-04 06:51:55',
                'permission_id' => $view_all_keylog_summary,
                'sequence_number' => 5,
            ),          
        ));
    }
}
