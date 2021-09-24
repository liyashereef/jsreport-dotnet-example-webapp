<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateMiscellaneousesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_miscellaneouses')->delete();

        \DB::table('candidate_miscellaneouses')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'veteran_of_armedforce' => 'No',
                'service_number' => null,
                'canadian_force' => null,
                'enrollment_date' => null,
                'release_date' => null,
                'item_release_number' => null,
                'rank_on_release' => null,
                'military_occupation' => null,
                'reason_for_release' => null,
                'dismissed' => 'No',
                'explanation_dismissed' => null,
                'limitations' => 'No',
                'limitation_explain' => null,
                'criminal_convicted' => 'No',
                'offence' => null,
                'offence_date' => null,
                'offence_location' => null,
                'disposition_granted' => null,
                'career_interest' => '1 - Commissionaires is a temporary stop in my career. I have no long term plans.',
                'other_roles' => 'Yes',
                'created_at' => '2018-07-25 12:52:16',
                'updated_at' => '2018-07-25 12:52:16',
            ),
        ));

    }
}
