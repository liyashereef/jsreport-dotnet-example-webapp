<?php

namespace Modules\Recruitment\Database\Seeders;

use Carbon\Carbon;
use App\Services\HelperService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsRecruitmentCandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rec_candidate_credential = HelperService::getPermissionId('rec-candidate-credential');
        $rec_candidate_tracking_summary = HelperService::getPermissionId('rec-candidate-tracking-summary');
        $rec_view_all_candidates = HelperService::getPermissionId('rec-view-all-candidates');
        $rec_track_all_candidates = HelperService::getPermissionId('rec-track-all-candidates');
        $rec_view_all_candidates_candidate_onboardingstatus = HelperService::getPermissionId('rec-view-all-candidates-candidate-onboardingstatus');
        $rec_create_candidate_credential = HelperService::getPermissionId('rec-create-candidate-credential');
        $rec_edit_candidate_credential = HelperService::getPermissionId('rec-edit-candidate-credential');
        $rec_delete_candidate_credential = HelperService::getPermissionId('rec-delete-candidate-credential');
        $rec_candidate_delete_job_application = HelperService::getPermissionId('rec-candidate-delete-job-application');
        $rec_candidate_approval = HelperService::getPermissionId('rec-candidate-approval');
        $rec_edit_candidate = HelperService::getPermissionId('rec-edit-candidate');
        $rec_candidate_screening_summary = HelperService::getPermissionId('rec-candidate-screening-summary');
        $rec_candidate_transition_process = HelperService::getPermissionId('rec_candidate_transition_process');
        $rec_candidate_rate_screening_question_answers = HelperService::getPermissionId('rec-candidate-rate-screening-question-answers');

        $module_id = HelperService::getModuleId('Recruitment');

        \DB::table('module_permissions')->where('module_id', $module_id)
        ->whereIn('permission_id', [
            $rec_candidate_credential,
            $rec_candidate_tracking_summary,
            $rec_view_all_candidates,
            $rec_track_all_candidates,
            $rec_view_all_candidates_candidate_onboardingstatus,
            $rec_create_candidate_credential,
            $rec_edit_candidate_credential,
            $rec_delete_candidate_credential,
            $rec_candidate_delete_job_application,
            $rec_candidate_approval,
            $rec_edit_candidate,
            $rec_candidate_screening_summary,
            $rec_candidate_transition_process,
            $rec_candidate_rate_screening_question_answers
        ])->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Candidate Credential',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_candidate_credential,
                'sequence_number' => 14,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'Candidate Onboarding Status',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_candidate_tracking_summary,
                'sequence_number' => 15,
            ),
            2 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Candidates in Candidates Summary',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_view_all_candidates,
                'sequence_number' => 16,
            ),
            3 => array(
                'module_id' => $module_id,
                'permission_description' => 'Track All Candidates',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_track_all_candidates,
                'sequence_number' => 17,
            ),
            4 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Candidates in Candidate Onboarding Status',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>   $rec_view_all_candidates_candidate_onboardingstatus,
                'sequence_number' => 18,
            ),
            5 => array(
                'module_id' => $module_id,
                'permission_description' => 'Create Candidate Credential',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>   $rec_create_candidate_credential,
                'sequence_number' => 19,
            ),
            6 => array(
                'module_id' => $module_id,
                'permission_description' => 'Edit Candidate Credential',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>   $rec_edit_candidate_credential,
                'sequence_number' => 20,
            ),
            7 => array(
                'module_id' => $module_id,
                'permission_description' => 'Delete Candidate Credential',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>   $rec_delete_candidate_credential,
                'sequence_number' => 21,
            ),
            8 => array(
                'module_id' => $module_id,
                'permission_description' => 'Delete Candidate Application',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>   $rec_candidate_delete_job_application,
                'sequence_number' => 22,
            ),
            9 => array(
                'module_id' => $module_id,
                'permission_description' => 'Candidate Approval',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>   $rec_candidate_approval,
                'sequence_number' => 23,
            ),
            10 => array(
                'module_id' => $module_id,
                'permission_description' => 'Edit Candidate Screening Form',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>   $rec_edit_candidate,
                'sequence_number' => 24,
            ),
            11 => array(
                'module_id' => $module_id,
                'permission_description' => 'Candidate Screening Summary',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>   $rec_candidate_screening_summary,
                'sequence_number' => 25,
            ),
            12 => array(
                  'module_id' => $module_id,
                  'permission_description' => 'Candidate Transition Process',
                  'created_at' =>  Carbon::now(),
                  'updated_at' =>  Carbon::now(),
                  'permission_id' =>   $rec_candidate_transition_process,
                  'sequence_number' => 26,
            ),
            13 => array(
                    'module_id' => $module_id,
                  'permission_description' => 'Rate Screening Question Answers',
                  'created_at' =>  Carbon::now(),
                  'updated_at' =>  Carbon::now(),
                  'permission_id' =>   $rec_candidate_rate_screening_question_answers,
                  'sequence_number' => 27,
            )
        ));

    }
}
