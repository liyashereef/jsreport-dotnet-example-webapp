<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeRatingLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('employee_rating_lookups')->delete();

        \DB::table('employee_rating_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'rating' => 'Does Not Meet Expectations',
                'score' => 1,
                'shortname'=>'DNME',
                'created_at' => '2017-12-29 06:28:47',
                'updated_at' => '2017-12-29 06:28:47',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'rating' => 'Marginally Meet Expectations',
                'score' => 2,
                'shortname'=>'MME',
                'created_at' => '2017-12-29 06:28:47',
                'updated_at' => '2017-12-29 06:28:47',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'rating' => 'Meet Expectations',
                'score' => 3,
                'shortname'=>'ME',
                'created_at' => '2017-12-29 06:28:47',
                'updated_at' => '2017-12-29 06:28:47',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'rating' => 'Exceeds Expectations',
                'score' => 4,
                'shortname'=>'EE',
                'created_at' => '2017-12-29 06:28:47',
                'updated_at' => '2017-12-29 06:28:47',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'rating' => 'Far Exceeds Expectations',
                'score' => 5,
                'shortname'=>'FEE',
                'created_at' => '2017-12-29 06:28:47',
                'updated_at' => '2017-12-29 06:28:47',
                'deleted_at' => null,
            ),
        ));

    }
}
