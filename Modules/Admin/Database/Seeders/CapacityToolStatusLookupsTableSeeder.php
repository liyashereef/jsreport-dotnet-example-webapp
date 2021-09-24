<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CapacityToolStatusLookupsTableSeeder extends Seeder
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
        \DB::table('capacity_tool_status_lookups')->delete();
        \DB::table('capacity_tool_status_lookups')->insert([

            0 => [
                'id' => 1,
                'value' => 'This work meets my personal performance expectations and is consistent with my peers',
                'short_name' => 'Work meets my personal performance expectations',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            1 => [
                'id' => 2,
                'value' => "This work exceeds my personal performance expectations and puts me in the top 15% of high performers in the organization.   This work contributes to the company's strategic objectives in a measurable way.",
                'short_name' => 'Work exceeds my personal performance expectations',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            2 => [
                'id' => 3,
                'value' => "This work far exceeds expectations and significantly sets me in the top 5% of the highest performers in the organization.  This work is significantly contributing in a measurable way to the company's strategic objectives.",
                'short_name' => 'Work far exceeds expectations ',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],

        ]);

    }
}
