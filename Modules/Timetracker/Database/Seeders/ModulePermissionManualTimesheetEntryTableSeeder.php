<?php

namespace Modules\Timetracker\Database\Seeders;

use App\Services\HelperService;
use Illuminate\Database\Seeder;

class ModulePermissionManualTimesheetEntryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission_id = HelperService::getPermissionId('view_manual_timesheet_entry');
        $module_id = \App\Services\HelperService::getModuleId('Time Tracker');
        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Manual Timesheet Entry',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $permission_id,
                'sequence_number' => 102,
            ),
        ));
    }
}
