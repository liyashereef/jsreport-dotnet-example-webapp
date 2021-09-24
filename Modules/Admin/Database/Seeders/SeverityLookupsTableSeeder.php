<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SeverityLookupsTableSeeder extends Seeder
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
        \DB::table('severity_lookups')->delete();
        \DB::table('severity_lookups')->insert([

            0 => [
                'id' => 1,
                'severity' => 'Critical',
                'short_name' => 'Critical',
                'value' => 1,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            1 => [
                'id' => 2,
                'severity' => 'Major',
                'short_name' => 'Major',
                'value' => 2,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            2 => [
                'id' => 3,
                'severity' => 'Moderate',
                'short_name' => 'Moderate',
                'value' => 3,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            3 => [
                'id' => 4,
                'severity' => 'Minor',
                'short_name' => 'Minor',
                'value' => 4,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],

        ]);

    }
}
