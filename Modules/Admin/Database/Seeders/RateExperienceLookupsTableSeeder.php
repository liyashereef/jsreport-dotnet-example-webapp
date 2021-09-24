<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class RateExperienceLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('rate_experience_lookups')->delete();

        \DB::table('rate_experience_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'experience_ratings' => 'My experience was very positive but looking for new oppurtunities',
                'score' => 5,
                'created_at' => '2017-12-27 06:03:59',
                'updated_at' => '2017-12-27 06:03:59',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'experience_ratings' => 'My experience was generally positive',
                'score' => 4,
                'created_at' => '2017-12-27 06:04:06',
                'updated_at' => '2017-12-27 06:04:06',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'experience_ratings' => 'My experience was neutral-neither good or bad',
                'score' => 3,
                'created_at' => '2017-12-27 06:04:13',
                'updated_at' => '2017-12-27 06:04:13',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'experience_ratings' => 'My experience was somewhat negative-the employer has room for improvement',
                'score' => 2,
                'created_at' => '2017-12-27 06:04:21',
                'updated_at' => '2017-12-27 06:04:21',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'experience_ratings' => 'My experience was very bad-my previous employer didn\'t treat their employees well',
                'score' => 1,
                'created_at' => '2017-12-27 06:04:27',
                'updated_at' => '2017-12-27 06:04:27',
                'deleted_at' => null,
            ),
        ));

    }
}
