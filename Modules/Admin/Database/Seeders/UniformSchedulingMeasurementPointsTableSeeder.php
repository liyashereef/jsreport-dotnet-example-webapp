<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UniformSchedulingMeasurementPointsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('uniform_scheduling_measurement_points')->delete();
        \DB::table('uniform_scheduling_measurement_points')->insert(
            [
                0 => [
                    'id' => 1,
                    'name' => 'Neck',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('y-m-d H:i:s')
                ],
                1 => [
                    'id' => 2,
                    'name' => 'Chest',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('y-m-d H:i:s')
                ],
                2 => [
                    'id' => 3,
                    'name' => 'Arm',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('y-m-d H:i:s')
                ],
                3 => [
                    'id' => 4,
                    'name' => 'Waist',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('y-m-d H:i:s')
                ],
                4 => [
                    'id' => 5,
                    'name' => 'Inside Leg',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('y-m-d H:i:s')
                ],
                5 => [
                    'id' => 6,
                    'name' => 'Hip',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('y-m-d H:i:s')
                ]
            ]
        );

    }
}
