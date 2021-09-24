<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecRateExperienceLookupsTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_rate_experience_lookups')->delete();

        \DB::connection('mysql_rec')->table('rec_rate_experience_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'experience_ratings' => 'My experience was very positive but looking for new opportunities',
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
                'experience_ratings' => 'My experience was neutral - Neither good nor bad',
                'score' => 3,
                'created_at' => '2017-12-27 06:04:13',
                'updated_at' => '2017-12-27 06:04:13',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'experience_ratings' => 'My experience was somewhat negative - The employer has room for improvement',
                'score' => 2,
                'created_at' => '2017-12-27 06:04:21',
                'updated_at' => '2017-12-27 06:04:21',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'experience_ratings' => 'My experience was very bad - My previous employer didn\'t treat their employees well',
                'score' => 1,
                'created_at' => '2017-12-27 06:04:27',
                'updated_at' => '2017-12-27 06:04:27',
                'deleted_at' => null,
            ),
        ));
    }
}
