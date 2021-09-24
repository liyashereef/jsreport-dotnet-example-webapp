<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\Services\HelperService;

class BonusPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'manage_bonus_settings']);
        Permission::create(['name' => 'view_bonus_reports']);

        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());

        $manage_bonus_settings = HelperService::getPermissionId('manage_bonus_settings');
        $module_id = HelperService::getModuleId('HR Analytics');
        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Manage Bonus Settings',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $manage_bonus_settings,
                'sequence_number' => 294,
            ),

        ));

        $view_bonus_reports = HelperService::getPermissionId('view_bonus_reports');
        $module_id = HelperService::getModuleId('HR Analytics');
        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Bonus Reports',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $view_bonus_reports,
                'sequence_number' => 295,
            ),

        ));
    }
}
