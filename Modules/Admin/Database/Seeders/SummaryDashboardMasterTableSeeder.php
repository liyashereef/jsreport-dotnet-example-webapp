<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class SummaryDashboardMasterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('summary_dashboard_master')->delete();
        \DB::table('summary_dashboard_master')->insert([
            [
                'id' => 1,
                'name' => 'Site Turnover',
                'machine_name' => 'site-turn-over',
                'threshold_type' => 2,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'name' => 'Schedule Infraction',
                'machine_name' => 'schedule-infraction',
                'threshold_type' => 1,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'name' => 'Operational Dashboard Matrix',
                'machine_name' => 'operational-dashboard-matrix',
                'threshold_type' => 1,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 4,
                'name' => 'Total Work Hours',
                'machine_name' => 'total-work-hours',
                'threshold_type' => 1,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            [
                'id' => 5,
                'name' => 'Earned Billings',
                'machine_name' => 'earned-billings',
                'threshold_type' => 1,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
