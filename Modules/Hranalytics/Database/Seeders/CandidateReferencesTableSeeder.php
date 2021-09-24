<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateReferencesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_references')->delete();

        \DB::table('candidate_references')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'reference_name' => 'Shelby',
                'reference_employer' => 'employer',
                'reference_position' => 'manager',
                'contact_phone' => '(333)333-3333',
                'contact_email' => 'shelby@ymail.com',
                'created_at' => null,
                'updated_at' => null,
            ),
        ));

    }
}
