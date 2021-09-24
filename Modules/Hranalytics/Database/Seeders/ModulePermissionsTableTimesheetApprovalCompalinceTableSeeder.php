<?php

namespace Modules\Hranalytics\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionsTableTimesheetApprovalCompalinceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_timesheet_approval_compalince = HelperService::getPermissionId('view_timesheet_approval_compalince');
        $module_id = HelperService::getModuleId('HR Analytics');
        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 46,
                'module_id' => $module_id,
                'permission_description' => 'View Timesheet Approval Compliance',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $view_timesheet_approval_compalince,
                'sequence_number' => 97,
            ),

        ));
        //
    }
}
