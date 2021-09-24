<?php

namespace Modules\FMDashboard\Database\Seeders;

use App\Services\HelperService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddCoursesWidgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        //Roles and permissions
        Permission::create(['name' => 'view_courses_widget']);
        Permission::create(['name' => 'view_job_tickets_widget']);
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());

        //Module permissions.
        $view_courses_widget = HelperService::getPermissionId('view_courses_widget');
        $view_job_tickets_widget = HelperService::getPermissionId('view_job_tickets_widget');
        $module_id = HelperService::getModuleId('FM Dashboard');
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'View Courses Widget',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_courses_widget,
                'sequence_number' => 8,
            ],
            [
                'module_id' => $module_id,
                'permission_description' => 'View Job Tickets Widget',
                'created_at' => '2019-07-25 12:22:00',
                'permission_id' => $view_job_tickets_widget,
                'sequence_number' => 9,
            ]
        ]);
    }

}
