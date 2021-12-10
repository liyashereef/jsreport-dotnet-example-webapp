<?php

namespace Modules\VisitorLog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class VisitorLogModuleTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $module_id = \App\Services\HelperService::getModuleId('Visitor Log');
        if (empty($module_id)) {
            \DB::table('modules')->insert([
                [
                    'name' => 'Visitor Log',
                    'created_at' => \Carbon::now(),
                    'updated_at' => \Carbon::now(),
                ]
            ]);
        }

        Permission::create(['name' => 'view_visitorlog']);
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
