<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateEducationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_educations')->delete();

        \DB::table('candidate_educations')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'start_date_education' => '2018-07-20',
                'end_date_education' => '2018-07-21',
                'grade' => 'grade',
                'program' => 'program',
                'school' => 'Ap #543-4490',
                'created_at' => null,
                'updated_at' => null,
            ),
        ));

    }
}
