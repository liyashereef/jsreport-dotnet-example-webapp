<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SubmissionReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('contract_submission_reasons')->delete();
        
        \DB::table('contract_submission_reasons')->insert(array (
            0 => 
            array (
                'id' => 1,
                'reason' => 'New Contract',
                'sequence' => 0,
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'reason' => 'Contract Amendment',
                'sequence' => 1,
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'reason' => 'Other',
                'sequence' => 2,
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            )
        ));
    }
}
