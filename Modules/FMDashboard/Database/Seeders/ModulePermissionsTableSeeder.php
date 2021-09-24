<?php

namespace Modules\FMDashboard\Database\Seeders;

use App\Models\CustomPermission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $view_time_widget = $this->getPermissionId('view_time_widget');
       $view_hr_widget = $this->getPermissionId('view_hr_widget');
       $view_site_dashboard_widget = $this->getPermissionId('view_site_dashboard_widget');
       $view_incident_summary_widget = $this->getPermissionId('view_incident_summary_widget');
       $view_incident_priority_widget = $this->getPermissionId('view_incident_priority_widget');
       $view_site_metrics_widget = $this->getPermissionId('view_site_metrics_widget');
       $view_timesheet_reconciliation_widget = $this->getPermissionId('view_timesheet_reconciliation_widget');  

       $module_id = \App\Services\HelperService::getModuleId('FM Dashboard');
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'View Time Widget',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_time_widget,
                'sequence_number' => 1,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View HR Widget',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_hr_widget,
                'sequence_number' => 2,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View Site Dashboard Widget',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_site_dashboard_widget,
                'sequence_number' => 3,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View Incident Summary Widget',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_incident_summary_widget,
                'sequence_number' => 4,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View Incident Priority Widget',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_incident_priority_widget,
                'sequence_number' => 5,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View Site Metrics Widget',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_site_metrics_widget,
                'sequence_number' => 6,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View Timesheet Reconciliation Widget',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_timesheet_reconciliation_widget,
                'sequence_number' => 7,
            ],
        ]);
    }

    public function getPermissionId($permission_name)
    {
        $permission_id = CustomPermission::where('name', $permission_name)->value('id');
        return $permission_id;
    }
}
