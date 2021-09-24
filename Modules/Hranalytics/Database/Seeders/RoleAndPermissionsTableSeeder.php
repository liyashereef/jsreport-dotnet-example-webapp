<?php

namespace Modules\Hranalytics\Database\Seeders;

use App\Services\SeederService;
use Illuminate\Database\Seeder;
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
        $permissions = array(
            'remove_manager_rating',
            'remove_client_rating',
        );
        SeederService::seedPermissions($permissions);

        $sadmin = Role::findByName('super_admin');
        $sadmin->givePermissionTo(Permission::all());
        $admin = Role::findByName('admin');
        $admin->givePermissionTo(Permission::all());
    }
}
