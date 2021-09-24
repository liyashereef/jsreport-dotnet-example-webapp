<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateSecurityClearancesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_security_clearances')->delete();

        \DB::table('candidate_security_clearances')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'born_outside_of_canada' => 'No',
                'work_status_in_canada' => 'Canadian Citizen',
                'years_lived_in_canada' => '6',
                'prepared_for_security_screening' => 'No',
                'no_clearance' => 'Yes',
                'no_clearance_explanation' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text.',
                'created_at' => '2018-07-25 12:52:16',
                'updated_at' => '2018-07-25 12:52:16',
            ),
        ));

    }
}
