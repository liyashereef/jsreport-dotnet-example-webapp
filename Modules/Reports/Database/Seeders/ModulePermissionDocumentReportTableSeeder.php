<?php

namespace Modules\Reports\Database\Seeders;

use App\Services\HelperService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ModulePermissionDocumentReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_all_site_document_report_id = HelperService::getPermissionId('view_all_site_document_report');
        $view_allocated_site_document_report_id = HelperService::getPermissionId('view_allocated_site_document_report');
        $module_id = \App\Services\HelperService::getModuleId('Reports');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View All Site Document Report',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_all_site_document_report_id,
                'sequence_number' => 104,
            ),
            1 => array(
                //'id' => 54,
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Site Document Report',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_allocated_site_document_report_id,
                'sequence_number' => 105,
            ),
        ));
    }
}
