<?php

namespace Modules\ClientApp\Database\Seeders; 

use Illuminate\Database\Seeder;
use App\Services\HelperService;

class ModulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client_app_login_id = HelperService::getPermissionId('client_app_login');
        $view_all_customers_clientapp_id = HelperService::getPermissionId('view_all_customers_clientapp');
        $view_allocated_customers_clientapp_id = HelperService::getPermissionId('view_allocated_customers_clientapp');

        $module_id = \App\Services\HelperService::getModuleId('Client App');
        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Client App Login',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $client_app_login_id,
                'sequence_number' => 1,
            ),
             1 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View All Customers',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $view_all_customers_clientapp_id,
                'sequence_number' => 2,
            ),
              2 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Customers',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $view_allocated_customers_clientapp_id,
                'sequence_number' => 3,
            ),
        ));
    }
}
