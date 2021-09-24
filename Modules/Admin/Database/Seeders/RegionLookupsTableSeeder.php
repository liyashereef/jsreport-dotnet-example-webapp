<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class RegionLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('region_lookups')->delete();

        \DB::table('region_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'region_name' => 'Ontario',
                'region_description' => 'Ontario',
                'created_at' => '2017-12-27 06:05:41',
                'updated_at' => '2017-12-27 06:05:41',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'region_name' => 'British Columbia',
                'region_description' => 'British Columbia',
                'created_at' => '2017-12-27 06:05:48',
                'updated_at' => '2017-12-27 06:05:48',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'region_name' => 'Alberta',
                'region_description' => 'Alberta',
                'created_at' => '2017-12-27 06:05:55',
                'updated_at' => '2017-12-27 06:05:55',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'region_name' => 'Nova Scotia',
                'region_description' => 'Nova Scotia',
                'created_at' => '2017-12-27 06:06:01',
                'updated_at' => '2017-12-27 06:06:01',
                'deleted_at' => null,
            ),
        ));
    }
}
