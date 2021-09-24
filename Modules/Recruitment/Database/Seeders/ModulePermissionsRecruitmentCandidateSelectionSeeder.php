<?php

namespace Modules\Recruitment\Database\Seeders;

use Carbon\Carbon;
use App\Services\HelperService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsRecruitmentCandidateSelectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rec_candidate_selection = HelperService::getPermissionId('rec-candidate-selection');
        $rec_candidate_uniform_shipment = HelperService::getPermissionId('rec-candidate-uniform-shipment');

        $module_id = HelperService::getModuleId('Recruitment');

        \DB::table('module_permissions')->where('module_id', $module_id)
        ->whereIn('permission_id', [
            $rec_candidate_selection,
            $rec_candidate_uniform_shipment
        ])->delete();

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Candidate Selection',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_candidate_selection,
                'sequence_number' => 15,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'Candidate Uniform Shipment',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' =>  $rec_candidate_uniform_shipment,
                'sequence_number' => 16,
            )
        ));

    }
}
