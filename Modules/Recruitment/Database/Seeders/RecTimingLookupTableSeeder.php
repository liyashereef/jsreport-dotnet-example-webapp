<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecTimingLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_timing_lookups')->delete();

        \DB::connection('mysql_rec')->table('rec_timing_lookups')->insert(array (
            0 =>
            array (
                'id' => 1,
                'timings' => 'Training must be completed within 90 days',
                'created_at' => '2017-12-27 06:05:06',
                'updated_at' => '2017-12-27 06:05:06',
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'timings' => 'Training must be completed within 30 days',
                'created_at' => '2017-12-27 06:05:14',
                'updated_at' => '2017-12-27 06:05:14',
                'deleted_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'timings' => 'Training must be completed before onboarding',
                'created_at' => '2017-12-27 06:05:22',
                'updated_at' => '2017-12-27 06:05:22',
                'deleted_at' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'timings' => 'Training must be today',
                'created_at' => '2017-12-27 06:05:29',
                'updated_at' => '2017-12-27 06:05:29',
                'deleted_at' => NULL,
            ),
        ));
    }
}
