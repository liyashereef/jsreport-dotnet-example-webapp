<?php

namespace Modules\Chat\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionsChatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        Permission::create(['name' => 'view_chat']);
        Permission::create(['name' => 'view_chat_menu']);
        Permission::create(['name' => 'view_chat_history']);
        Permission::create(['name' => 'view_chat_in_api']);
       
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
