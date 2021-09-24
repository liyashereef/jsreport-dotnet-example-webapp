<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CapacityToolTaskFrequencyLookupsTableSeeder extends Seeder
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
        \DB::table('capacity_tool_task_frequency_lookups')->delete();
        \DB::table('capacity_tool_task_frequency_lookups')->insert([

            0 => [
                'id' => 1,
                'value' => 'Ad Hoc',
                'sequence_number' => 1,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            1 => [
                'id' => 2,
                'value' => 'Daily',
                'sequence_number' => 2,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            2 => [
                'id' => 3,
                'value' => 'Weekly',
                'sequence_number' => 3,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            3 => [
                'id' => 4,
                'value' => 'Monthly',
                'sequence_number' => 5,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            4 => [
                'id' => 5,
                'value' => 'Quarterly',
                'sequence_number' => 6,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            5 => [
                'id' => 6,
                'value' => 'Annually',
                'sequence_number' => 7,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            6 => [
                'id' => 7,
                'value' => 'Bi-Weekly',
                'sequence_number' => 4,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
        ]);
    }
}
