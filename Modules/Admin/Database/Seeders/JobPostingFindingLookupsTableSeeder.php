<?php

namespace Modules\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JobPostingFindingLookupsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('job_post_finding_lookups')->delete();

        \DB::table('job_post_finding_lookups')->insert(
            array(
                0 => array(
                    'id' => 1,
                    'job_post_finding' => 'I found out through Indeed.ca',
                    'order_sequence' => 1,
                    'is_editable' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
                1 => array(
                    'id' => 2,
                    'job_post_finding' => 'I found out through commissionaires website',
                    'order_sequence' => 2,
                    'is_editable' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
                2 => array(
                    'id' => 3,
                    'job_post_finding' => 'I was referred to this job by a friend',
                    'order_sequence' => 3,
                    'is_editable' => 0,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
                3 => array(
                    'id' => 4,
                    'job_post_finding' => 'I found out about this job through another job board',
                    'order_sequence' => 4,
                    'is_editable' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
            )
        );
    }
}
