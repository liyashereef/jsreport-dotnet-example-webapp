<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class TrackingProcessLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tracking_process_lookups')->delete();
        
        \DB::table('tracking_process_lookups')->insert(array (
            0 => 
            array (
                'id' => 1,
            	'process_steps' => 'Pass Secondary Screening (Filled Out The Online Tool)',
		'step_number' => 1,
                'created_at' => '2018-01-16 00:00:00',
                'updated_at' => '2018-01-16 00:00:00',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'process_steps' => 'Phone Screen Scheduled',
		'step_number' => 2,
                'created_at' => '2018-01-16 00:00:00',
                'updated_at' => '2018-01-16 00:00:00',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'process_steps' => 'Phone Screen Completed',
		'step_number' => 3,
                'created_at' => '2018-01-16 09:04:30',
                'updated_at' => '2018-01-16 09:04:30',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'process_steps' => 'Candidate Confirms Site Interview with HR',
		'step_number' => 4,
                'created_at' => '2018-01-16 09:04:44',
                'updated_at' => '2018-01-16 09:04:44',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'process_steps' => 'Candidate Shows Up - Interview with HR',
		'step_number' => 5,
                'created_at' => '2018-01-16 09:05:07',
                'updated_at' => '2018-01-16 09:05:15',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'process_steps' => 'Candidate Passes Interview',
		'step_number' => 6,
                'created_at' => '2018-01-16 09:05:43',
                'updated_at' => '2018-01-16 09:05:43',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'process_steps' => 'References Are Verified By HR',
		'step_number' => 7,
                'created_at' => '2018-01-16 09:05:56',
                'updated_at' => '2018-01-16 09:05:56',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'process_steps' => 'Interview With Site Supervisor',
		'step_number' => 8,
                'created_at' => '2018-01-16 09:06:10',
                'updated_at' => '2018-01-16 09:06:10',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'process_steps' => 'Candidate Passes Site Supervisor Interview',
		'step_number' => 9,
                'created_at' => '2018-01-16 09:06:27',
                'updated_at' => '2018-01-16 09:06:27',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'process_steps' => 'Candidate Scheduled For Onboarding',
		'step_number' => 10,
                'created_at' => '2018-01-16 09:06:38',
                'updated_at' => '2018-01-16 09:06:38',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'process_steps' => 'Application Form Completed',
		'step_number' => 11,
                'created_at' => '2018-01-16 09:06:51',
                'updated_at' => '2018-01-16 09:06:51',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'process_steps' => 'Onboarding Completed',
		'step_number' => 12,
                'created_at' => '2018-01-16 09:07:10',
                'updated_at' => '2018-01-16 09:07:52',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'process_steps' => 'All Documentation Submitted',
		'step_number' => 13,
                'created_at' => '2018-01-16 09:08:35',
                'updated_at' => '2018-01-16 09:08:35',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'process_steps' => 'Uniform/ Kitting Completed',
		'step_number' => 14,
                'created_at' => '2018-01-16 09:08:47',
                'updated_at' => '2018-01-16 09:08:47',
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'process_steps' => 'Compass Entry - Employee Number Issued',
		'step_number' => 15,
                'created_at' => '2018-01-16 09:09:00',
                'updated_at' => '2018-01-16 09:09:00',
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'process_steps' => 'Deployed To Site',
		'step_number' => 16,
                'created_at' => '2018-01-16 09:09:13',
                'updated_at' => '2018-01-16 09:09:13',
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'process_steps' => 'OJT Completed',
		'step_number' => 17,
                'created_at' => '2018-01-16 09:09:30',
                'updated_at' => '2018-01-16 09:09:30',
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'process_steps' => 'Candidate Transitioned',
		'step_number' => 18,
                'created_at' => '2018-01-16 09:09:42',
                'updated_at' => '2018-01-16 09:10:02',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}
