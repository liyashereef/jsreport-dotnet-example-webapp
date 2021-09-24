<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateExperiencesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_experiences')->delete();

        \DB::table('candidate_experiences')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'current_employee_commissionaries' => 'No',
                'employee_number' => null,
                'currently_posted_site' => null,
                'position' => null,
                'hours_per_week' => null,
                'applied_employment' => 'No',
                'position_applied' => null,
                'start_date_position_applied' => null,
                'end_date_position_applied' => null,
                'employed_by_corps' => 'No',
                'position_employed' => null,
                'start_date_employed' => null,
                'end_date_employed' => null,
                'location_employed' => null,
                'employee_num' => null,
                'created_at' => '2018-07-25 12:52:16',
                'updated_at' => '2018-07-25 12:52:16',
            ),
        ));

    }
}
