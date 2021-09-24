<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class KpiMasterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \DB::table('kpi_masters')->delete();

        \DB::table('kpi_masters')->insert([
            [
                'id' => 1,
                'name' => 'Incident Compliance',
                'machine_name' => 'incident-compliance',
                'threshold_type'=>2,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'name' => 'Site Metric',
                'machine_name' => 'site-metric',
                'threshold_type'=>1,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'name' => 'Schedule Compliance',
                'machine_name' => 'schedule-compliance',
                'threshold_type'=>2,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 4,
                'name' => 'Client Survey',
                'machine_name' => 'client-survey',
                'threshold_type'=>1,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 5,
                'name' => 'Training Compliance',
                'machine_name' => 'training-compliance',
                'threshold_type'=>2,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 6,
                'name' => 'Performance Management',
                'machine_name' => 'performance-management',
                'threshold_type'=>1,
                'is_active' => true,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}

