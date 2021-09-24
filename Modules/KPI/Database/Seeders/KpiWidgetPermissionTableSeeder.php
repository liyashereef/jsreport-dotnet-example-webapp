<?php

namespace Modules\KPI\Database\Seeders;

use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class KpiWidgetPermissionTableSeeder extends Seeder
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
        Permission::create(['name' => 'view_kpi_widget']);

        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());

        //Module permissions.
        $p = HelperService::getPermissionId('view_kpi_widget');
        $module_id = \App\Services\HelperService::getModuleId('Landing Page');
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'View KPI Widget',
                'created_at' => Carbon::now(),
                'permission_id' => $p,
                'sequence_number' => 1,
            ],

        ]);
    }
}
