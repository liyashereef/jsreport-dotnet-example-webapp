<?php

namespace Modules\Management\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsTableUserSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_skill_edit_id = HelperService::getPermissionId('user_skill_edit');

        $module_id = HelperService::getModuleId('Management');

        \DB::table('module_permissions')->insert(array(

            0 => array(
                'module_id' =>$module_id,
                'permission_description' => 'User Skill Edit',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $user_skill_edit_id,
                'sequence_number' => 116,
            ),
            

        ));
    }
}
