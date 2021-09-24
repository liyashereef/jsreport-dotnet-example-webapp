<?php

namespace Modules\Recruitment\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecProcessStepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_process_steps')->delete();

        \DB::connection('mysql_rec')->table('rec_process_steps')->insert(array(
            0 => array(
                'id' => 1,
                'step_order' => 1,
                'step_name' => 'login_assigned',
                'display_name' => 'Login Assigned',
                'notes' => 'Automatically generated message based on when recruiter assigns user Id/password and auto email alert',
                'type'=>1,
                'route'=>null,
                'tab_id'=>null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'step_order' => 2,
                'step_name' => 'user_access',
                'display_name' => 'User Access',
                'notes' => 'Automatically generated when user logs in for first time (ie. Submit user ID and password and authenticated)',
                'type'=>1,
                'route'=>null,
                'tab_id'=>null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'step_order' => 3,
                'step_name' => 'profile_completed',
                'display_name' => 'Profile Completed',
                'notes' => 'Automatically generated when they save "Profile"',
                'type'=>1,
                'route'=>'/form/profile',
                'tab_id'=>1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'step_order' => 4,
                'step_name' => 'screening_questions',
                'display_name' => 'Screening Questions',
                'notes' => 'Automatically generated when they save "Screening Questions"',
                'type'=>1,
                'route'=>'/form/screening',
                'tab_id'=>2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'step_order' => 5,
                'step_name' => 'personality',
                'display_name' => 'Personality',
                'notes' => 'Automatically generated when they save "Personality"',
                'type'=>1,
                'route'=>'/form/personality',
                'tab_id'=>3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'step_order' => 6,
                'step_name' => 'competency',
                'display_name' => 'Competency',
                'notes' => 'Automatically generated when they save "Competency"',
                'type'=>1,
                'route'=>'/form/competency',
                'tab_id'=>4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
              6 => array(
                'id' => 7,
                'step_order' => 7,
                'step_name' => 'rate_screening_questions',
                'display_name' => 'Rate Screening Questions',
                'notes' => 'Recruiter must manually rate screening questions and english proficiency.',
                'type'=>1,
                'route'=>'/form/apply',
                'tab_id'=>5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            7=> array(
                'id' => 8,
                'step_order' => 8,
                'step_name' => 'screen_applications',
                'display_name' => 'Screen Applications',
                'notes' => ' Recruiter confirms COMPLETENESS. Then simply clicks "schedule" for interview',
                'type'=>1,
                'route'=>'/form/apply',
                'tab_id'=>6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            8 => array(
                'id' => 9,
                'step_order' => 9,
                'step_name' => 'apply_for_jobs',
                'display_name' => 'Apply For Jobs',
                'notes' => 'Automatically generated when they save "Submit Job Postings"',
                'type'=>1,
                'route'=>'/form/apply',
                'tab_id'=>7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            9 => array(
                'id' => 10,
                'step_order' => 10,
                'step_name' => 'hr_interview_scheduled',
                'display_name' => 'HR Interview Scheduled',
                'notes' => 'When Recruiter clicks schedule link',
                'type'=>1,
                'route'=>'/form/appliedjobs',
                'tab_id'=>7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            10=> array(
                'id' => 11,
                'step_order' => 11,
                'step_name' => 'interview_completed',
                'display_name' => 'Interview Completed',
                'notes' => 'Zoom Link via API and Recruiter follows standard interview script. "Green" when recording from zoom uploaded.',
                'type'=>1,
                'route'=>'/form/appliedjobs',
                'tab_id'=>7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            11 => array(
                'id' => 12,
                'step_order' => 12,
                'step_name' => 'references_validated',
                'display_name' => 'References Validated',
                'notes' => 'References completed',
                'type'=>1,
                'route'=>'/form/appliedjobs',
                'tab_id'=>7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            12 => array(
                'id' => 13,
                'step_order' => 13,
                'step_name' => 'onboarding_initiated',
                'display_name' => 'Onboarding Initiated',
                'notes' => 'Initiated Onboarding By Recruiter - select "dropdown" that proceeds with onboarding.',
                'type'=>1,
                'route'=>'/form/appliedjobs',
                'tab_id'=>9,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            13 => array(
                'id' => 14,
                'step_order' => 14,
                'step_name' => 'enrollment_completed',
                'display_name' => 'Enrollment Completed',
                'notes' => 'Automatically generated when they save "Enrollment Forms" (must have all forms uploaded to save application)',
                'type'=>1,
                'route'=>'/onboarding/enrollment',
                'tab_id'=>10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            14 => array(
                'id' => 15,
                'step_order' => 15,
                'step_name' => 'security_clearance_completed',
                'display_name' => 'Security Clearance Completed',
                'notes' => 'Automatically generated when they save "Security Clearance" (must have all forms uploaded to save application)',
                'type'=>1,
                'route'=>'/onboarding/securityclearence',
                'tab_id'=>11,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            15 => array(
                'id' => 16,
                'step_order' => 16,
                'step_name' => 'tax_forms_completed',
                'display_name' => 'Tax Forms Completed',
                'notes' => 'Automatically generated when they save "Tax Forms" (must have all forms uploaded to save application)',
                'type'=>1,
                'route'=>'/onboarding/taxforms',
                'tab_id'=>12,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
             16 => array(
                'id' => 17,
                'step_order' => 17,
                'step_name' => 'upload_attachments',
                'display_name' => 'Upload Attachments',
                'notes' => 'Automatically generated when they save "Upload"',
                'type'=>1,
                'route'=>'/onboarding/taxforms',
                'tab_id'=>13,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            17 => array(
                'id' => 18,
                'step_order' => 18,
                'step_name' => 'uniform_measurement_completed',
                'display_name' => 'Uniform Measurement Completed',
                'notes' => 'Automatically generated when they save "Uniform Measurements" are saved',
                'type'=>1,
                'route'=>'/onboarding/uniform',
                'tab_id'=>14,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            18 => array(
                'id' => 19,
                'step_order' => 19,
                'step_name' => 'uniform_processed',
                'display_name' => 'Uniform Processed',
                'notes' => 'Automatically generated when they save "Uniform Measurements" are saved',
                'type'=>1,
                'route'=>'/onboarding/uniform',
                'tab_id'=>15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            19 => array(
                'id' => 20,
                'step_order' => 20,
                'step_name' => 'uniform_received',
                'display_name' => 'Uniform Received',
                'notes' => 'Quartermaster calls to update status of uniforms',
                'type'=>1,
                'route'=>'/onboarding/uniform',
                'tab_id'=>16,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            20 => array(
                'id' => 21,
                'step_order' => 21,
                'step_name' => 'core_training_completed',
                'display_name' => 'Core Training Completed',
                'notes' => 'Automatically generated when they finish all exams (100%) - Mostly for VIDEO POST(no other training)',
                'type'=>1,
                'route'=>'/onboarding/uniform',
                'tab_id'=>17,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            21 => array(
                'id' => 22,
                'step_order' => 22,
                'step_name' => 'candidate_conversion',
                'display_name' => 'Candidate Conversion',
                'notes' => 'Automatically generated',
                'type'=>1,
                'route'=>'/onboarding/uniform',
                'tab_id'=>18,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            22 => array(
                'id' => 23,
                'step_order' => 23,
                'step_name' => 'onboarding_meeting_scheduled',
                'display_name' => 'Onboarding Meeting Scheduled',
                'notes' => 'When Recruiter clicks scheduled link',
                'type'=>0,
                'route'=>'/onboarding/uniform',
                'tab_id'=>19,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            23 => array(
                'id' => 24,
                'step_order' => 24,
                'step_name' => 'onboarding_meeting_completed',
                'display_name' => 'Onboarding Meeting Completed',
                'notes' => 'Zoom Link. Jerry or RM schedules onboarding interview - saved as ZOOM link.',
                'type'=>0,
                'route'=>'/onboarding/uniform',
                'tab_id'=>20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            )

        ));
    }
}
