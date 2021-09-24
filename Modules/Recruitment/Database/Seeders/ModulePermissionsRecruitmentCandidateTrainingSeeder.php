<?php

namespace Modules\Recruitment\Database\Seeders;

use Carbon\Carbon;
use App\Services\HelperService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsRecruitmentCandidateTrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rec_candidate_training = HelperService::getPermissionId('rec-view-candidate-training');

        $module_id = HelperService::getModuleId('Recruitment');

        \DB::table('module_permissions')->where('module_id', $module_id)
        ->whereIn('permission_id', [
            $rec_candidate_training
        ])->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Candidate Training',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_candidate_training,
                'sequence_number' => 20,
            )
        ));

    }
}
