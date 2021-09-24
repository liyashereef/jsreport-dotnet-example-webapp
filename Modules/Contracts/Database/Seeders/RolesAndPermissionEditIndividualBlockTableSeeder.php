<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionEditIndividualBlockTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        Permission::create(['name' => 'edit_contract_information']);
        Permission::create(['name' => 'edit_contract_business_information']);
        Permission::create(['name' => 'edit_contract_regionalmanager_information']);
        Permission::create(['name' => 'edit_contract_sales_information']);
        Permission::create(['name' => 'edit_contract_clientcontact_information']);
        Permission::create(['name' => 'edit_contract_contractterms_information']);
        Permission::create(['name' => 'edit_contract_pricingdefinition_information']);
        Permission::create(['name' => 'edit_contract_pricingdetails_information']);
        Permission::create(['name' => 'edit_contract_holiday_information']);
        Permission::create(['name' => 'edit_contract_po_information']);
        Permission::create(['name' => 'edit_contract_supervisor_information']);
        Permission::create(['name' => 'edit_contract_scopeofwork_information']);
        Permission::create(['name' => 'edit_contract_amendment_information']);

        // $this->call("OthersTableSeeder");
        $s_admin_role = Role::findByName('super_admin');
        $s_admin_role->givePermissionTo(Permission::all());
        $admin_role = Role::findByName('admin');
        $admin_role->givePermissionTo(Permission::all());
    }
}
