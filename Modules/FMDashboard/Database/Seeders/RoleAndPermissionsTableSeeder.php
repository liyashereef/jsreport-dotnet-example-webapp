<?php

namespace Modules\FMDashboard\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        Permission::create(['name' => 'view_fmdashboard']);
        Permission::create(['name' => 'view_time_widget']);
        Permission::create(['name' => 'view_hr_widget']);
        Permission::create(['name' => 'view_site_dashboard_widget']);
        Permission::create(['name' => 'view_incident_summary_widget']);
        Permission::create(['name' => 'view_incident_priority_widget']);
        Permission::create(['name' => 'view_site_metrics_widget']);
        Permission::create(['name' => 'view_timesheet_reconciliation_widget']);
        
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
