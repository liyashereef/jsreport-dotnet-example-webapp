<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsRecruitmentJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         \DB::table('permissions')->whereIn('name', ['view_recruitment','rec-create-job','rec-list-jobs-from-all','rec-edit-job','rec-archive-job','rec-job-approval','rec-assign_job_ticket','rec-job-attachement-settings','rec-hr-tracking','rec-job-tracking-summary','rec-candidate-mapping','rec-view_all_candidates_candidate_geomapping','rec-hr-tracking-detailed-view','rec-delete-hr-tracking'])->delete();
        Permission::create(['name' => 'view_recruitment']);
        Permission::create(['name' => 'rec-create-job']);
        Permission::create(['name' => 'rec-list-jobs-from-all']);
        Permission::create(['name' => 'rec-edit-job']);
        Permission::create(['name' => 'rec-archive-job']);
        Permission::create(['name' => 'rec-job-approval']);
        Permission::create(['name' => 'rec-assign_job_ticket']);
        Permission::create(['name' => 'rec-job-attachement-settings']);
        Permission::create(['name' => 'rec-hr-tracking']);
        Permission::create(['name' => 'rec-job-tracking-summary']);
        Permission::create(['name' => 'rec-candidate-mapping']);
        Permission::create(['name' => 'rec-view_all_candidates_candidate_geomapping']);
        Permission::create(['name' => 'rec-hr-tracking-detailed-view']);
        Permission::create(['name' => 'rec-delete-hr-tracking']);
       

        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
