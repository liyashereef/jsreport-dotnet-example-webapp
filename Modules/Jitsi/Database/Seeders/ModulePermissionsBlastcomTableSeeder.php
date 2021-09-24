<?php

namespace Modules\Jitsi\Database\Seeders;

use App\Models\CustomPermission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsBlastcomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $create_blastcom_all_customers = $this->getPermissionId('create_blastcom_all_customers');
        $create_blastcom_allocated_customers = $this->getPermissionId('create_blastcom_allocated_customers');
        $view_blastcom_reports = $this->getPermissionId('view_blastcom_reports');


        $module_id = \App\Services\HelperService::getModuleId('CGL Meet');
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'BlastCom - All Clients',
                'created_at' => \Carbon::now(),
                'permission_id' => $create_blastcom_all_customers,
                'sequence_number' => 272,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'BlastCom - Allocated Clients',
                'created_at' => \Carbon::now(),
                'permission_id' => $create_blastcom_allocated_customers,
                'sequence_number' => 273,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View BlastCom Reports',
                'created_at' => \Carbon::now(),
                'permission_id' => $view_blastcom_reports,
                'sequence_number' => 274,
            ]
        ]);
    }

    public function getPermissionId($permission_name)
    {
        $permission_id = CustomPermission::where('name', $permission_name)->value('id');
        return $permission_id;
    }
}
