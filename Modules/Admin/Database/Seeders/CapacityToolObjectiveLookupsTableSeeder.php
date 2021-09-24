<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CapacityToolObjectiveLookupsTableSeeder extends Seeder
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
        \DB::table('capacity_tool_objective_lookups')->delete();
        \DB::table('capacity_tool_objective_lookups')->insert([

            0 => [
                'id' => 1,
                'value' => 'Achieved',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            1 => [
                'id' => 2,
                'value' => 'Pending Outcome',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            2 => [
                'id' => 3,
                'value' => 'Did Not Achieve',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            3 => [
                'id' => 4,
                'value' => 'Ongoing',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],

        ]);
    }
}
