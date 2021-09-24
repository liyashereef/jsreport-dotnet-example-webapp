<?php

namespace Modules\VideoPost\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionAlterVideoPostpermissionNameTableSeeder extends Seeder
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
        $view_video_post_in_app = HelperService::getPermissionId('view_video_post_in_app');
        $module_id = HelperService::getModuleId('Video Post');

        \DB::table('module_permissions')
            ->where('module_id', $module_id)
            ->whereIn('permission_id', [
                $view_all_customers_in_video_post,
                $view_allocated_customers_in_video_post,
                $view_video_post_in_app,
            ])
            ->delete();

        \DB::table('module_permissions')->insert(array(
                0 => array(
                    'module_id' => $module_id,
                    'permission_description' => 'View All Customers in Video Post',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'permission_id' => $view_all_customers_in_video_post,
                    'sequence_number' => 227,
                ),
                1=> array(
                    'module_id' => $module_id,
                    'permission_description' => 'View Allocated Customers in Video Post',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'permission_id' => $view_allocated_customers_in_video_post,
                    'sequence_number' => 228,
                ),
                2=> array(
                        'module_id' => $module_id,
                        'permission_description' => 'View Video Post in App',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'permission_id' => $view_video_post_in_app,
                        'sequence_number' => 232,
                )

            ));


    }
}
