<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateSkillsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_skills')->delete();

        \DB::table('candidate_skills')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'skill_id' => 1,
                'skill_level' => 'Basic Knowledge',
                'created_at' => '2018-07-25 12:52:17',
                'updated_at' => '2018-07-25 12:52:17',
            ),
            1 => array(
                'id' => 2,
                'candidate_id' => 1,
                'skill_id' => 2,
                'skill_level' => 'Basic Knowledge',
                'created_at' => '2018-07-25 12:52:17',
                'updated_at' => '2018-07-25 12:52:17',
            ),
            2 => array(
                'id' => 3,
                'candidate_id' => 1,
                'skill_id' => 3,
                'skill_level' => 'Basic Knowledge',
                'created_at' => '2018-07-25 12:52:17',
                'updated_at' => '2018-07-25 12:52:17',
            ),
            3 => array(
                'id' => 4,
                'candidate_id' => 1,
                'skill_id' => 4,
                'skill_level' => 'Basic Knowledge',
                'created_at' => '2018-07-25 12:52:17',
                'updated_at' => '2018-07-25 12:52:17',
            ),
            4 => array(
                'id' => 5,
                'candidate_id' => 1,
                'skill_id' => 5,
                'skill_level' => 'Basic Knowledge',
                'created_at' => '2018-07-25 12:52:17',
                'updated_at' => '2018-07-25 12:52:17',
            ),
            5 => array(
                'id' => 6,
                'candidate_id' => 1,
                'skill_id' => 6,
                'skill_level' => 'Basic Knowledge',
                'created_at' => '2018-07-25 12:52:17',
                'updated_at' => '2018-07-25 12:52:17',
            ),
            6 => array(
                'id' => 7,
                'candidate_id' => 1,
                'skill_id' => 7,
                'skill_level' => 'Basic Knowledge',
                'created_at' => '2018-07-25 12:52:17',
                'updated_at' => '2018-07-25 12:52:17',
            ),
        ));

    }
}
