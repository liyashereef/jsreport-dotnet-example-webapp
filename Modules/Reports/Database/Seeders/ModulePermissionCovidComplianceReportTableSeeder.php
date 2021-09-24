<?php

namespace Modules\Reports\Database\Seeders;

use App\Services\HelperService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionCovidComplianceReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission_id = HelperService::getPermissionId('view_covid_compliance_report');
        $module_id = \App\Services\HelperService::getModuleId('Reports');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Covid Compliance Report',
                'created_at' => '2018-11-13 06:51:55',
                'updated_at' => '2018-11-13 06:51:55',
                'permission_id' => $permission_id,
                'sequence_number' => 102,
            ),
        ));
    }
}
