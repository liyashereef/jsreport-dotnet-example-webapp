<?php

namespace Modules\Client\Database\Seeders;


use App\Models\CustomPermission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class ModulePermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $view_allocated_clientsurvey = $this->getPermissionId('view_allocated_clientsurvey');
        $view_all_clientsurvey = $this->getPermissionId('view_all_clientsurvey');
        $add_allocated_clientsurvey = $this->getPermissionId('add_allocated_clientsurvey');
        $add_all_clientsurvey = $this->getPermissionId('add_all_clientsurvey');
        $module_id = \App\Services\HelperService::getModuleId('Client');
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Client Survey',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_allocated_clientsurvey,
                'sequence_number' => 221,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View All Client Survey',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_all_clientsurvey,
                'sequence_number' => 222,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'Add Allocated Client Survey',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $add_allocated_clientsurvey,
                'sequence_number' => 223,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'Add All Client Survey',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $add_all_clientsurvey,
                'sequence_number' => 224,
            ]
        ]);
    }

    public function getPermissionId($permission_name)
    {
        $permission_id = CustomPermission::where('name', $permission_name)->value('id');
        return $permission_id;
    }
}
