<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TrackingProcessStepTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('rfp_process_steps')->delete();
        
        \DB::table('rfp_process_steps')->insert(array (
            0 => 
            array (
                'id' => 1,
            	'process_steps' => 'RFP Summary Entered',
		        'step_number' => 1,
                'created_at' => '2019-08-27 00:00:00',
                'updated_at' => '2019-08-27 00:00:00',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'process_steps' => 'RFP Approved And Resources Allocated',
		        'step_number' => 2,
                'created_at' => '2019-08-27 00:00:00',
                'updated_at' => '2019-08-27 00:00:00',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'process_steps' => 'Template Downloaded',
		        'step_number' => 3,
                'created_at' => '2019-08-27 09:04:30',
                'updated_at' => '2019-08-27 09:04:30',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'process_steps' => 'Site Visit',
		        'step_number' => 4,
                'created_at' => '2019-08-27 09:04:44',
                'updated_at' => '2019-08-27 09:04:44',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'process_steps' => 'Question & Answer',
		        'step_number' => 5,
                'created_at' => '2019-08-27 09:05:07',
                'updated_at' => '2019-08-27 09:05:15',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'process_steps' => 'Pricing Model',
		        'step_number' => 6,
                'created_at' => '2019-08-27 09:05:43',
                'updated_at' => '2019-08-27 09:05:43',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'process_steps' => 'First Draft Completed',
		        'step_number' => 7,
                'created_at' => '2019-08-27 09:05:56',
                'updated_at' => '2019-08-27 09:05:56',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'process_steps' => 'Insurance, WSIB and Other Documents Completed',
		       'step_number' => 8,
                'created_at' => '2019-08-27 09:06:10',
                'updated_at' => '2019-08-27 09:06:10',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'process_steps' => 'Review Session With Executive',
		        'step_number' => 9,
                'created_at' => '2019-08-27 09:06:27',
                'updated_at' => '2019-08-27 09:06:27',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'process_steps' => 'Revisions And Edits',
		        'step_number' => 10,
                'created_at' => '2019-08-27 09:06:38',
                'updated_at' => '2019-08-27 09:06:38',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'process_steps' => 'Submit RFP',
		        'step_number' => 11,
                'created_at' => '2019-08-27 09:06:51',
                'updated_at' => '2019-08-27 09:06:51',
                'deleted_at' => NULL,
            ),
            
           
        ));
    }
}
