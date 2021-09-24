<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsDeleteDocumentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $delete_keys = \App\Services\HelperService::getPermissionId('delete_document');
        $module_id = \App\Services\HelperService::getModuleId('Documents');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Delete Document',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $delete_keys,
                'sequence_number' => 113,
            )
        ));
    }
}
