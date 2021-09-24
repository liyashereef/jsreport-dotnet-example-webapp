<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateEmploymentHistorysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_employment_historys')->delete();

        \DB::table('candidate_employment_historys')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'start_date' => '2018-07-18',
                'end_date' => '2018-07-21',
                'employer' => 'Lareina',
                'role' => 'admin',
                'duties' => 'management',
                'reason' => 'Retirement',
                'created_at' => null,
                'updated_at' => null,
            ),
        ));

    }
}
