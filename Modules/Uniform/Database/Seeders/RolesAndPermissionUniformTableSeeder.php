<?php

namespace Modules\Uniform\Database\Seeders;

use App\Services\SeederService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionUniformTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newPermissions = array(
            'view_uniform',
            'view_uniform_in_app',
            'view_ura_balance',
            'view_ura_transactions',
            'add_ura_debit_transaction',
            'add_ura_credit_transaction',
            'view_uniform_orders',
            'change_uniform_order_status'
        );
        SeederService::seedPermissions($newPermissions);

        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
