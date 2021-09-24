<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;

class ModulePermissionRFPCatalogueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_rfp_catalogue_id = HelperService::getPermissionId('view_rfp_catalogue');
        $create_rfp_catalogue_id = HelperService::getPermissionId('create_rfp_catalogue');
        $module_id = \App\Services\HelperService::getModuleId('Contracts');

        \DB::table('module_permissions')->where('module_id', $module_id)->whereIn('permission_id', [$create_rfp_catalogue_id,$view_rfp_catalogue_id])->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'Create RFP Catalog',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $create_rfp_catalogue_id,
                'sequence_number' => 109,
            ),
             1 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View RFP Catalog',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $view_rfp_catalogue_id,
                'sequence_number' => 110,
            ),
        ));
    }
}
