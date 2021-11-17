<?php

namespace Modules\VisitorLog\Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Services\HelperService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModulePermissionsOfVisitorLogMasterSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'visitor_log_admin']);
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());

        //Module permissions.
        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => HelperService::getModuleId('Admin'),
                'permission_description' => 'Visitor Log Masters',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => HelperService::getPermissionId('visitor_log_admin'),
                'sequence_number' => 296,
            )
        ));

    }
}
