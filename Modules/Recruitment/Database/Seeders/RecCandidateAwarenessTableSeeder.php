<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecCandidateAwarenessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_candidate_awareness')->delete();

        \DB::connection('mysql_rec')->table('rec_candidate_awareness')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                //'job_id' => 43,
                'fit_assessment_why_apply_for_this_job' => 'In Informatics, dummy data is benign information that does not contain any useful data, but serves to reserve space where real data is nominally present. Dummy data can be used as a placeholder for both testing and operational purposes. For testing, dummy data can also be used as stubs or pad to avoid software testing issues by ensuring that all variables and data fields are occupied. In operational use, dummy data may be transmitted for OPSEC purposes. Dummy data must be rigorously evaluated',
                'status' => 'Applied',
                'candidate_status' => null,
                'feedback_id' => null,
                'average_score' => 0.0,
                'proposed_wage' => null,
                // 'proposed_wage_high' => null,
                'job_reassigned_id' => null,
                'english_rating_id' => null,
                'created_at' => '2018-07-25 12:52:16',
                'updated_at' => '2018-07-25 12:53:10',
                'deleted_at' => null,
            ),
        ));
    }
}
