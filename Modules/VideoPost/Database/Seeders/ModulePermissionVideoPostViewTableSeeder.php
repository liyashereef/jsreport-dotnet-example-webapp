<?php

namespace Modules\VideoPost\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionVideoPostViewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $add_video_post = HelperService::getPermissionId('add_video_post');
        $edit_video_post = HelperService::getPermissionId('edit_video_post');
        $delete_video_post = HelperService::getPermissionId('delete_video_post');
        $view_video_post_in_app = HelperService::getPermissionId('view_video_post_in_app');
        $module_id = HelperService::getModuleId('Video Post');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Add Video Post',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $add_video_post,
                'sequence_number' => 229,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'Edit Video Post',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $edit_video_post,
                'sequence_number' => 230,
            ),
            2 => array(
                'module_id' => $module_id,
                'permission_description' => 'Delete Video Post',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $delete_video_post,
                'sequence_number' => 231,
            ),
            3 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Video Post In App',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_video_post_in_app,
                'sequence_number' => 232,
            ),
        ));
    }
}
