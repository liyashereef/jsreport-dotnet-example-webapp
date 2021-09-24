<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateLanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_languages')->delete();

        \DB::table('candidate_languages')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'language_id' => 1,
                'speaking' => 'C - Fluent - this is my native language.',
                'reading' => 'C - Fluent - this is my native language.',
                'writing' => 'C - Fluent - this is my native language.',
                'created_at' => '2018-07-25 12:52:17',
                'updated_at' => '2018-07-25 12:52:17',
            ),
            1 => array(
                'id' => 2,
                'candidate_id' => 1,
                'language_id' => 2,
                'speaking' => 'C - Fluent - this is my native language.',
                'reading' => 'D - No Knowledge.',
                'writing' => 'D - No Knowledge.',
                'created_at' => '2018-07-25 12:52:17',
                'updated_at' => '2018-07-25 12:52:17',
            ),
        ));

    }
}
