<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecCandidateWageExpectationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_candidate_wage_expectations')->delete();

        \DB::connection('mysql_rec')->table('rec_candidate_wage_expectations')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'wage_expectations_from' => '19',
                'wage_expectations_to' => '20',
                'wage_last_hourly' => '18',
                'current_paystub' => 'Yes',
                'wage_last_provider' => '1',
                'wage_last_provider_other' => null,
                'last_role_held' => '7',
                'explanation_wage_expectation' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.',
                'created_at' => '2018-07-25 12:52:16',
                'updated_at' => '2018-07-25 12:52:16',
            ),
        ));

    }
}
