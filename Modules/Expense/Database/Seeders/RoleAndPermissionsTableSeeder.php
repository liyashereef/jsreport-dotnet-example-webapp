<?php

namespace Modules\Expense\Database\Seeders;

use App\Services\SeederService;
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

        $permissions = [
            'view_expense',
            'expense_masters',
            'view_all_expense_claim',
            'view_allocated_expense_claim',
            'view_all_mileage_claim',
            'view_allocated_mileage_claim',
            'expense_send_statements'
        ];

        SeederService::seedPermissions($permissions);

        Role::findByName('super_admin')->givePermissionTo(Permission::all());
        Role::findByName('admin')->givePermissionTo(Permission::all());
        
    }
}
