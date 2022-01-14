<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class JobsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('jobs')->delete();

        \DB::table('jobs')->insert(array(
            0 => array(
                'id' => 41,
                'user_id' => 5,
                'unique_key' => 'METROSECTOR001',
                'open_position_id' => 2,
                'no_of_vaccancies' => 13,
                'job_description' => '*',
                'reason_id' => 1,
                'temp_code_id' => null,
                'permanent_id' => 13,
                'resign_id' => null,
                'terminate_id' => null,
                'area_manager' => 'Benjamin Alexander',
                'am_email' => 'balexanderAM@secture360.ca.ca',
                'requisition_date' => '2017-12-09',
                'customer_id' => 3,
                'requester' => 'Benjamin Alexander',
                'email' => 'balexander@secture360.ca.ca',
                'phone' => '(416)364-4496',
                'position' => '7',
                'employee_num' => '122514',
                'assignment_type_id' => 4,
                'required_job_start_date' => '2017-12-13',
                'time' => '12:00:00',
                'ongoing' => 'Yes',
                'end' => null,
                'training_id' => 2,
                'training_time' => '40',
                'training_timing_id' => 3,
                'course' => 'None',
                'notes' => 'They need to live near Whitby.',
                'shifts' => '["Days","Afternoons","Evenings","Overnight","Statutory holidays","Continental (12 Hours Shift)"]',
                'days_required' => '["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]',
                'criterias' => '[]',
                'vehicle' => 'No',
                'wage_low' => 17.5,
                'wage_high' => 17.5,
                'remarks' => 'Need to be ready for January 1, 2018 go live.',
                'active' => 1,
                'status' => 'approved',
                'required_attachments' => null,
                'approved_by' => 10,
                'hr_rep_id' => 5,
                'approved_at' => '2017-12-09 10:30:35',
                'created_at' => '2017-12-09 10:29:49',
                'updated_at' => '2017-12-15 12:12:11',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 43,
                'user_id' => 5,
                'unique_key' => 'TORONCUSMIS001',
                'open_position_id' => 9,
                'no_of_vaccancies' => 20,
                'job_description' => '*',
                'reason_id' => 1,
                'temp_code_id' => null,
                'permanent_id' => 10,
                'resign_id' => null,
                'terminate_id' => null,
                'area_manager' => 'Benjamin Alexander',
                'am_email' => 'balexanderAM@secture360.ca.ca',
                'requisition_date' => '2017-12-10',
                'customer_id' => 4,
                'requester' => 'Benjamin Alexander',
                'email' => 'balexander@secture360.ca.ca',
                'phone' => '(416)364-4496',
                'position' => '7',
                'employee_num' => '122514',
                'assignment_type_id' => 4,
                'required_job_start_date' => '2017-12-11',
                'time' => '12:00:00',
                'ongoing' => 'Yes',
                'end' => null,
                'training_id' => 2,
                'training_time' => '40',
                'training_timing_id' => 1,
                'course' => 'None',
                'notes' => null,
                'shifts' => '["Days","Afternoons","Evenings","Overnight","Statutory holidays","Continental (12 Hours Shift)"]',
                'days_required' => '["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]',
                'criterias' => '[]',
                'vehicle' => 'No',
                'wage_low' => 15.800000000000001,
                'wage_high' => 15.800000000000001,
                'remarks' => null,
                'active' => 1,
                'status' => 'approved',
                'required_attachments' => null,
                'approved_by' => 10,
                'hr_rep_id' => 5,
                'approved_at' => '2017-12-10 18:24:20',
                'created_at' => '2017-12-10 18:23:05',
                'updated_at' => '2017-12-15 12:20:38',
                'deleted_at' => null,
            ),
        ));

    }
}
