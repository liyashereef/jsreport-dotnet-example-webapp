<?php

namespace Modules\Hranalytics\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionsTableGeomappingFieldsPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_dob_in_employee_geomapping = HelperService::getPermissionId('view_dob_in_employee_geomapping');
        $view_age_in_employee_geomapping = HelperService::getPermissionId('view_age_in_employee_geomapping');
        $view_veteran_status_in_employee_geomapping = HelperService::getPermissionId('view_veteran_status_in_employee_geomapping');
        $view_employee_rating_in_employee_geomapping = HelperService::getPermissionId('view_employee_rating_in_employee_geomapping');
        $view_clearance_type_in_employee_geomapping = HelperService::getPermissionId('view_clearance_type_in_employee_geomapping');
        $view_candidate_score_in_candidate_geomapping = HelperService::getPermissionId('view_candidate_score_in_candidate_geomapping');
        $module_id = HelperService::getModuleId('HR Analytics');
        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Dob In Employee Geomapping',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $view_dob_in_employee_geomapping,
                'sequence_number' => 257,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Age In Employee Geomapping',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $view_age_in_employee_geomapping,
                'sequence_number' => 258,
            ),
            2 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Employee Rating In Employee Geomapping',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $view_employee_rating_in_employee_geomapping,
                'sequence_number' => 259,
            ),
            3 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Clearance type In Employee Geomapping',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $view_clearance_type_in_employee_geomapping,
                'sequence_number' => 260,
            ),
            4 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Candidate Score In Candidate Geomapping',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $view_candidate_score_in_candidate_geomapping,
                'sequence_number' => 261,
            ),
            5 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Veteran Status In Employee Geomapping',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $view_veteran_status_in_employee_geomapping,
                'sequence_number' => 262,
            ),

        ));
    }
}
