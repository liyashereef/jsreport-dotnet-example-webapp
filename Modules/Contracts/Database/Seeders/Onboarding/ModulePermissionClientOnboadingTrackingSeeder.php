<?php

namespace Modules\Contracts\Database\Seeders\Onboarding;

use Illuminate\Database\Seeder;
use App\Services\HelperService;

class ModulePermissionClientOnboadingTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module_id = \App\Services\HelperService::getModuleId('Contracts');
        $configure_client_onboarding_tracking = HelperService::getPermissionId('configure_client_onboarding_tracking');
        $view_assigned_client_onboarding_steps = HelperService::getPermissionId('view_assigned_client_onboarding_steps');
        $view_all_client_onboarding_steps = HelperService::getPermissionId('view_all_client_onboarding_steps');
        $update_client_onboarding_step_status = HelperService::getPermissionId('update_client_onboarding_step_status');


        \DB::table('module_permissions')
            ->where('module_id', $module_id)
            ->whereIn('permission_id', [
                $configure_client_onboarding_tracking,
                $view_assigned_client_onboarding_steps,
                $view_all_client_onboarding_steps,
                $update_client_onboarding_step_status
            ])
            ->delete();

        \DB::table('module_permissions')->insert(array(
            array(
                'module_id' => $module_id,
                'permission_description' => 'Configure Client Onboarding Tracking',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'permission_id' => $configure_client_onboarding_tracking,
                'sequence_number' => 120,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'View Assigned Client Onboarding Tracking Steps',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'permission_id' => $view_assigned_client_onboarding_steps,
                'sequence_number' => 121,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'View All Client Onboarding Tracking Steps',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'permission_id' => $view_all_client_onboarding_steps,
                'sequence_number' => 122,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'Update Client Onboarding Tracking Status',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'permission_id' => $update_client_onboarding_step_status,
                'sequence_number' => 123,
            ),

        ));
    }
}
