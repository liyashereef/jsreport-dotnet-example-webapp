<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;

class RecJobProcessLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::connection('mysql_rec')->table('rec_job_process_lookups')->delete();
        
        \DB::connection('mysql_rec')->table('rec_job_process_lookups')->insert(array (
            0 =>
            array (
                'id' => 1,
                'process_name' => 'Job Placement Form Completed',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            1 =>
            array (
                'id' => 2,
                'process_name' => 'Job Description Using Standard Template',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            2 =>
            array (
                'id' => 3,
                'process_name' => 'Job Posted On Job Site And Internally',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            3 =>
            array (
                'id' => 4,
            'process_name' => 'Primary Screening Completed (Resume Review)',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            4 =>
            array (
                'id' => 5,
            'process_name' => 'Secondary Screening Completed (Template/Questionnaire)',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            5 =>
            array (
                'id' => 6,
                'process_name' => 'Candidates Shortlisted For Interviews',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            6 =>
            array (
                'id' => 7,
                'process_name' => 'HR Interviews Set Up',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            7 =>
            array (
                'id' => 8,
                'process_name' => 'HR Background/Reference Checks',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            8 =>
            array (
                'id' => 9,
                'process_name' => 'Operator Interviews Conducted',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            9 =>
            array (
                'id' => 10,
                'process_name' => 'Feedback Consolidated, Recommendation Made',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            10 =>
            array (
                'id' => 11,
                'process_name' => 'Job Offer Made/Accepted',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            11 =>
            array (
                'id' => 12,
                'process_name' => 'Candidate Onboarding/CQC',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            12 =>
            array (
                'id' => 13,
            'process_name' => 'Candidate On The Job Training (OJT)',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            13 =>
            array (
                'id' => 14,
                'process_name' => 'Candidate Scheduled/Onboarded',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
        ));
    }
}
