<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsRecruitmentJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rec_create_job = HelperService::getPermissionId('rec-create-job');
        $rec_list_jobs_from_all = HelperService::getPermissionId('rec-list-jobs-from-all');
        $rec_edit_job = HelperService::getPermissionId('rec-edit-job');
        $rec_archive_job = HelperService::getPermissionId('rec-archive-job');
        $rec_job_approval = HelperService::getPermissionId('rec-job-approval');
        $rec_assign_job_ticket = HelperService::getPermissionId('rec-assign_job_ticket');
        $rec_job_attachement_settings = HelperService::getPermissionId('rec-job-attachement-settings');
        $rec_hr_tracking = HelperService::getPermissionId('rec-hr-tracking');
        $rec_job_tracking_summary = HelperService::getPermissionId('rec-job-tracking-summary');
        $rec_candidate_mapping = HelperService::getPermissionId('rec-candidate-mapping');
        $rec_view_all_candidates_candidate_geomapping = HelperService::getPermissionId('rec-view_all_candidates_candidate_geomapping');
        $rec_hr_tracking_detailed_view= HelperService::getPermissionId('rec-hr-tracking-detailed-view');
        $rec_delete_hr_tracking= HelperService::getPermissionId('rec-delete-hr-tracking');
        
        $module_id = HelperService::getModuleId('Recruitment');
        \DB::table('module_permissions')->where('module_id', $module_id)
                                        ->whereIn('permission_id', [$rec_create_job,$rec_list_jobs_from_all,$rec_edit_job,$rec_archive_job,
                                            $rec_job_approval,$rec_assign_job_ticket,$rec_job_attachement_settings,$rec_hr_tracking,$rec_job_tracking_summary,$rec_candidate_mapping,$rec_view_all_candidates_candidate_geomapping,
                                            $rec_hr_tracking_detailed_view,$rec_delete_hr_tracking])
                                        ->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Post Job',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_create_job,
                'sequence_number' => 1,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'View all Job Requisitions',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_list_jobs_from_all,
                'sequence_number' => 2,
            ),
            2 => array(
                'module_id' => $module_id,
                'permission_description' => 'Edit Job',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_edit_job,
                'sequence_number' => 3,
            ),
            3 => array(
                'module_id' => $module_id,
                'permission_description' => 'Archive Job',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_archive_job,
                'sequence_number' => 4,
            ),
            
            4 => array(
                'module_id' => $module_id,
                'permission_description' => 'Change Job Status',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_job_approval,
                'sequence_number' => 5,
            ),
            5 => array(
                'module_id' => $module_id,
                'permission_description' => 'Assign Job Ticket',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_assign_job_ticket,
                'sequence_number' => 6,
            ),
            6 => array(
                'module_id' => $module_id,
                'permission_description' => 'Mandatory Attachment Settings',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_job_attachement_settings,
                'sequence_number' => 7,
            ),
            7 => array(
                'module_id' => $module_id,
                'permission_description' => 'HR Tracking (Job & Candidate)',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_hr_tracking,
                'sequence_number' => 8,
            ),
            8 => array(
                'module_id' => $module_id,
                'permission_description' => 'Status Summary (Job Summary)',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_job_tracking_summary,
                'sequence_number' => 9,
            ),
            9 => array(
                'module_id' => $module_id,
                'permission_description' => 'Candidate Mapping',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_candidate_mapping,
                'sequence_number' => 10,
            ),
            10 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Candidates in Candidate Geomapping',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_view_all_candidates_candidate_geomapping,
                'sequence_number' => 11,
            ),
            11 => array(
                'module_id' => $module_id,
                'permission_description' => 'View HR Tracking',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_hr_tracking_detailed_view,
                'sequence_number' => 12,
            ),
             12 => array(
                'module_id' => $module_id,
                'permission_description' => 'Delete HR Tracking Step',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_delete_hr_tracking,
                'sequence_number' => 13,
            ),
        ));
    }
}
