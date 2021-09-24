<?php

namespace Modules\Contracts\Database\Seeders;

use App\Models\CustomPermission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsEditContractTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $edit_contract = $this->getPermissionId('edit_contract');
        $module_id = \App\Services\HelperService::getModuleId('contracts');
        // $this->call("OthersTableSeeder");
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'Edit Contracts',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $edit_contract,
                'sequence_number' => 97,
            ]
        ]);
    }

    public function getPermissionId($permission_name)
    {
        $permission_id = CustomPermission::where('name', $permission_name)->value('id');
        return $permission_id;
    }
}
