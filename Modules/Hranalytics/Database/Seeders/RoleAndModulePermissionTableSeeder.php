<?php

namespace Modules\Hranalytics\Database\Seeders;

use App\Models\CustomPermission;
use Illuminate\Database\Seeder;
use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoleAndModulePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $removeManagerRating = HelperService::getPermissionId('remove_manager_rating');
        $removeClientRating = HelperService::getPermissionId('remove_client_rating');

        $module_id = \App\Services\HelperService::getModuleId('HR Analytics');
        
        DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'Delete Manager Rating',
                'created_at' => Carbon::now(),
                'permission_id' => $removeManagerRating,
                'sequence_number' => 278,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'Delete Client Rating',
                'created_at' => Carbon::now(),
                'permission_id' => $removeClientRating,
                'sequence_number' => 279,
            ]
        ]);
    }

    public function getPermissionId($permission_name)
    {
        $permission_id = CustomPermission::where('name', $permission_name)->value('id');
        return $permission_id;
    }
}
