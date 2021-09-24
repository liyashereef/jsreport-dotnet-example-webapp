<?php

namespace Modules\VideoPost\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionVideoPostViewsummaryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_video_post_summary = HelperService::getPermissionId('view_video_post_summary');

        $module_id = HelperService::getModuleId('Video Post');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Video Post Summary',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_video_post_summary,
                'sequence_number' => 226,
            ),

        ));
    }
}
