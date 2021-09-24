<?php

namespace Modules\Jitsi\Database\Seeders;

use App\Models\CustomPermission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsCGLMeetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_meeting_page = $this->getPermissionId('view_meeting_page');
        $view_scheduled_meeting_page = $this->getPermissionId('view_scheduled_meeting_page');


        $module_id = \App\Services\HelperService::getModuleId('CGL Meet');
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'Moderator Permission',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_meeting_page,
                'sequence_number' => 270,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'Manage Scheduled Meeting',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_scheduled_meeting_page,
                'sequence_number' => 271,
            ]
        ]);
    }

    public function getPermissionId($permission_name)
    {
        $permission_id = CustomPermission::where('name', $permission_name)->value('id');
        return $permission_id;
    }
}
