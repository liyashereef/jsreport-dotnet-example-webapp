<?php

namespace Modules\Hranalytics\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InsertSubmittedDatetoCandidateJobTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $candidateJobsObjects = \DB::table('candidate_jobs')->orderBy('id', 'ASC')->get();
        if (!empty($candidateJobsObjects)) {
            foreach ($candidateJobsObjects as $candidateObject) {
                if (empty($candidateObject->submitted_date)) {
                    \DB::table('candidate_jobs')->where('id', $candidateObject->id)->update(['submitted_date'=>$candidateObject->created_at]);
                }
            }
        }
    }
}
