<?php

namespace Modules\VideoPost\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionVideoPostingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_all_customers_in_video_post = HelperService::getPermissionId('view_all_customers_in_video_post');
        $view_allocated_customers_in_video_post = HelperService::getPermissionId('view_allocated_customers_in_video_post');
        $module_id = HelperService::getModuleId('Video Post');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Customers In Video Post',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_all_customers_in_video_post,
                'sequence_number' => 227,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Customers In Video Post',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_allocated_customers_in_video_post,
                'sequence_number' => 228,
            ),
        ));
    }
}
