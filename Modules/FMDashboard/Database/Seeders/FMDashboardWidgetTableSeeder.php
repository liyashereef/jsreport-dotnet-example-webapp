<?php

namespace Modules\FMDashboard\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FMDashboardWidgetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fm_dashboard_widgets')->delete();
        
        DB::table('fm_dashboard_widgets')->insert([
            [
                'id' => 1,
                'name' => 'Time',
                'section_name' => 'fcm_time',
                'permission' => 'view_time_widget',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
            [
                'id' => 2,
                'name' => 'Human Resource',
                'section_name' => 'fcm_hr',
                'permission' => 'view_hr_widget',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
            [
                'id' => 3,
                'name' => 'Site Dashboard',
                'section_name' => 'fcm_site_dashboard',
                'permission' => 'view_site_dashboard_widget',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
            [
                'id' => 4,
                'name' => 'Incident Summary',
                'section_name' => 'fcm_incident_summary',
                'permission' => 'view_incident_summary_widget',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
            [
                'id' => 5,
                'name' => 'Incident Priority',
                'section_name' => 'fcm_incident_priority',
                'permission' => 'view_incident_priority_widget',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
            [
                'id' => 6,
                'name' => 'Site Metrics',
                'section_name' => 'fcm_site_metrics',
                'permission' => 'view_site_metrics_widget',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
            [
                'id' => 7,
                'name' => 'Timesheet Reconciliation',
                'section_name' => 'fcm_timesheet_reconciliation',
                'permission' => 'view_timesheet_reconciliation_widget',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
            [
                'id' => 8,
                'name' => 'Courses',
                'section_name' => 'view_courses_widget',
                'permission' => 'view_courses_widget',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
            [
                'id' => 9,
                'name' => 'Job Ticket',
                'section_name' => 'view_job_tickets_widget',
                'permission' => 'view_job_tickets_widget',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
            [
                'id' => 10,
                'name' => 'Training Compliance',
                'section_name' => 'view_training_compliance',
                'permission' => 'view_training_compliance',
                'active' => '1',
                'created_at' => '2019-08-06 00:00:00',
                'updated_at' => NULL,
            ],
        ]);
    }
}