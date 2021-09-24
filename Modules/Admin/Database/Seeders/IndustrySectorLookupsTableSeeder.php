<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class IndustrySectorLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('industry_sector_lookups')->delete();

        \DB::table('industry_sector_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'industry_sector_name' => 'Finance',
                'created_at' => '2017-12-27 06:05:41',
                'updated_at' => '2017-12-27 06:05:41',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'industry_sector_name' => 'Government',
                'created_at' => '2017-12-27 06:05:48',
                'updated_at' => '2017-12-27 06:05:48',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'industry_sector_name' => 'Hospitality',
                'created_at' => '2017-12-27 06:05:55',
                'updated_at' => '2017-12-27 06:05:55',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'industry_sector_name' => 'IT',
                'created_at' => '2017-12-27 06:06:01',
                'updated_at' => '2017-12-27 06:06:01',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'industry_sector_name' => 'Manufacturing',
                'created_at' => '2017-12-27 06:06:08',
                'updated_at' => '2017-12-27 06:06:08',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'industry_sector_name' => 'Transportation',
                'created_at' => '2017-12-27 06:06:16',
                'updated_at' => '2017-12-27 06:06:16',
                'deleted_at' => null,
            ),
        ));

    }
}
