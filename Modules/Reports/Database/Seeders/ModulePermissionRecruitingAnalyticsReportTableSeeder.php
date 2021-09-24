<?php

namespace Modules\Reports\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionRecruitingAnalyticsReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission_id = HelperService::getPermissionId('view_recruiting_analytics_report');
        $module_id = HelperService::getModuleId('Reports');

        \DB::table('module_permissions')->where('module_id', $module_id)->where('permission_id', $permission_id)->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Recruiting Analytics Reports',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $permission_id,
                'sequence_number' => 103,
            ),
        ));
    }
}
