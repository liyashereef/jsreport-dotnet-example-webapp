<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecTrainingTimingLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_training_timing_lookups')->delete();

        \DB::connection('mysql_rec')->table('rec_training_timing_lookups')->insert(array (
            0 =>
            array (
                'id' => 1,
            'training' => 'External Training (Course related)',
                'created_at' => '2017-12-27 06:04:47',
                'updated_at' => '2017-12-27 06:04:47',
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'training' => 'OJT',
                'created_at' => '2017-12-27 06:04:54',
                'updated_at' => '2017-12-27 06:04:54',
                'deleted_at' => NULL,
            ),
        ));

    }
}
