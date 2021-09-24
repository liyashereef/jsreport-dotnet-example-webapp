<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CapacityToolWorkClassificationAreaLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        \DB::table('capacity_tool_work_classification_area_lookups')->delete();
        \DB::table('capacity_tool_work_classification_area_lookups')->insert([

            0 => [
                'id' => 1,
                'value' => 'Sales',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            1 => [
                'id' => 2,
                'value' => 'Customer Management',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            2 => [
                'id' => 3,
                'value' => 'Employee Management',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            3 => [
                'id' => 4,
                'value' => 'Finance And IT',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            4 => [
                'id' => 5,
                'value' => 'Support Services',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            5 => [
                'id' => 6,
                'value' => 'Marketing',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            6 => [
                'id' => 7,
                'value' => 'Travel Time',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            7 => [
                'id' => 8,
                'value' => 'Business Strategy',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            8 => [
                'id' => 9,
                'value' => 'Project Management',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            9 => [
                'id' => 10,
                'value' => 'Meetings',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            10 => [
                'id' => 11,
                'value' => 'Self Development',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            11 => [
                'id' => 12,
                'value' => 'Reporting And Analysis',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            12 => [
                'id' => 13,
                'value' => 'Email',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            13 => [
                'id' => 14,
                'value' => 'Process Engineering',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            14 => [
                'id' => 15,
                'value' => 'Administration',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],

        ]);
    }
}
