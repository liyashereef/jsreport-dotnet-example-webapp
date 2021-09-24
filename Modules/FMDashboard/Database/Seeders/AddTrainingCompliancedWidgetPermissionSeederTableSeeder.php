<?php

namespace Modules\FMDashboard\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddTrainingCompliancedWidgetPermissionSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Model::unguard();
        //Roles and permissions
        Permission::create(['name' => 'view_training_compliance']);
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());

        //Module permissions.
        $view_training_compliance = HelperService::getPermissionId('view_training_compliance');
        $module_id = HelperService::getModuleId('FM Dashboard');
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'View Training Compliance',
                'created_at' => '2019-09-06 00:00:00',
                'permission_id' => $view_training_compliance,
                'sequence_number' => 10,
            ],
           
        ]);
    }
}
