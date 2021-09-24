<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecCriteriaLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_criteria_lookups')->delete();

        \DB::connection('mysql_rec')->table('rec_criteria_lookups')->insert(array (
            0 =>
            array (
                'id' => 1,
                'criteria' => 'First Aid',
                'created_at' => '2017-12-27 06:05:41',
                'updated_at' => '2017-12-27 06:05:41',
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'criteria' => 'Guard Licence',
                'created_at' => '2017-12-27 06:05:48',
                'updated_at' => '2017-12-27 06:05:48',
                'deleted_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'criteria' => 'CPR',
                'created_at' => '2017-12-27 06:05:55',
                'updated_at' => '2017-12-27 06:05:55',
                'deleted_at' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'criteria' => 'Bilingual',
                'created_at' => '2017-12-27 06:06:01',
                'updated_at' => '2017-12-27 06:06:01',
                'deleted_at' => NULL,
            ),
            4 =>
            array (
                'id' => 5,
                'criteria' => 'Use of Force',
                'created_at' => '2017-12-27 06:06:08',
                'updated_at' => '2017-12-27 06:06:08',
                'deleted_at' => NULL,
            ),
            5 =>
            array (
                'id' => 6,
                'criteria' => 'CCTV Monitor',
                'created_at' => '2017-12-27 06:06:16',
                'updated_at' => '2017-12-27 06:06:16',
                'deleted_at' => NULL,
            ),
        ));
    }
}
