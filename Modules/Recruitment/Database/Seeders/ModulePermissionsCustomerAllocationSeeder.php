<?php

namespace Modules\Recruitment\Database\Seeders;

use Carbon\Carbon;
use App\Services\HelperService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsCustomerAllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rec_view_allocated_job_requisitions = HelperService::getPermissionId('rec-view-allocated-job-requisitions');
        $rec_view_allocated_candidates_summary = HelperService::getPermissionId('rec-view-allocated-candidates-summary');
        $rec_view_allocated_candidates_geomapping = HelperService::getPermissionId('rec-view-allocated-candidates-geomapping');
        $rec_view_allocated_candidates_tracking = HelperService::getPermissionId('rec-view-allocated-candidates-tracking');
      
        $module_id = HelperService::getModuleId('Recruitment');

        \DB::table('module_permissions')->where('module_id', $module_id)
        ->whereIn('permission_id', [
            $rec_view_allocated_job_requisitions,
            $rec_view_allocated_candidates_summary,
            $rec_view_allocated_candidates_geomapping,
            $rec_view_allocated_candidates_tracking,
        ])->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Job Requisition',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_view_allocated_job_requisitions,
                'sequence_number' => 28,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Candidate Summary',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_view_allocated_candidates_summary,
                'sequence_number' => 29,
            ),
            2 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Candidate Geomapping',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_view_allocated_candidates_geomapping,
                'sequence_number' => 30,
            ),
            3 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Candidate Tracking',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_view_allocated_candidates_tracking,
                'sequence_number' => 31,
            )
            
        ));
    }
}
