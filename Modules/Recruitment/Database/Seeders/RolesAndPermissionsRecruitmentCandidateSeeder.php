<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsRecruitmentCandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('permissions')->whereIn('name', [
            'rec-candidate-credential',
            'rec-candidate-tracking-summary',
            'rec-view-all-candidates',
            'rec-track-all-candidates',
            'rec-view-all-candidates-candidate-onboardingstatus',
            'rec-create-candidate-credential',
            'rec-edit-candidate-credential',
            'rec-delete-candidate-credential',
            'rec-candidate-delete-job-application',
            'rec-candidate-approval',
            'rec-edit-candidate',
            'rec-candidate-screening-summary',
            'rec_candidate_transition_process',
            'rec-candidate-rate-screening-question-answers'
            ])->delete();

        Permission::create(['name' => 'rec-candidate-credential']);
        Permission::create(['name' => 'rec-candidate-tracking-summary']);
        Permission::create(['name' => 'rec-view-all-candidates']);
        Permission::create(['name' => 'rec-track-all-candidates']);
        Permission::create(['name' => 'rec-view-all-candidates-candidate-onboardingstatus']);
        Permission::create(['name' => 'rec-create-candidate-credential']);
        Permission::create(['name' => 'rec-edit-candidate-credential']);
        Permission::create(['name' => 'rec-delete-candidate-credential']);
        Permission::create(['name' => 'rec-candidate-delete-job-application']);
        Permission::create(['name' => 'rec-candidate-approval']);
        Permission::create(['name' => 'rec-edit-candidate']);
        Permission::create(['name' => 'rec-candidate-screening-summary']);
        Permission::create(['name' => 'rec_candidate_transition_process']);
        Permission::create(['name' => 'rec-candidate-rate-screening-question-answers']);

        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
