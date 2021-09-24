<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use App\Models\CustomPermission;

class ModulePermissionEditIndividualBlockTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module_id = \App\Services\HelperService::getModuleId('Contracts');
        Model::unguard();
        $edit_contract_information = $this->getPermissionId('edit_contract_information');
        $edit_contract_business_information = $this->getPermissionId('edit_contract_business_information');
        $edit_contract_regionalmanager_information = $this->getPermissionId('edit_contract_regionalmanager_information');
        $edit_contract_sales_information = $this->getPermissionId('edit_contract_sales_information');
        $edit_contract_clientcontact_information = $this->getPermissionId('edit_contract_clientcontact_information');
        $edit_contract_contractterms_information = $this->getPermissionId('edit_contract_contractterms_information');
        $edit_contract_pricingdefinition_information = $this->getPermissionId('edit_contract_pricingdefinition_information');
        $edit_contract_pricingdetails_information = $this->getPermissionId('edit_contract_pricingdetails_information');
        $edit_contract_holiday_information = $this->getPermissionId('edit_contract_holiday_information');
        $edit_contract_po_information = $this->getPermissionId('edit_contract_po_information');
        $edit_contract_supervisor_information = $this->getPermissionId('edit_contract_supervisor_information');
        $edit_contract_scopeofwork_information = $this->getPermissionId('edit_contract_scopeofwork_information');
        $edit_contract_amendment_information = $this->getPermissionId('edit_contract_amendment_information');
        $module_id = \App\Services\HelperService::getModuleId('contracts');
        // $this->call("OthersTableSeeder");
        \DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'Edit Contracts Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_information,
                'sequence_number' => 226,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Business Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_business_information,
                'sequence_number' => 227,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Regional Manager Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_regionalmanager_information,
                'sequence_number' => 228,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Sales Manager Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_sales_information,
                'sequence_number' => 229,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Client Contact Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_clientcontact_information,
                'sequence_number' => 230,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Contract Terms Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_contractterms_information,
                'sequence_number' => 231,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Pricing Definition Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_pricingdefinition_information,
                'sequence_number' => 232,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Pricing Details Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_pricingdetails_information,
                'sequence_number' => 233,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Contract Holidays Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_holiday_information,
                'sequence_number' => 234,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Contract Purchase Order Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_po_information,
                'sequence_number' => 235,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Contracts Supervisor Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_supervisor_information,
                'sequence_number' => 236,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Contract Scope Of Work Information',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_scopeofwork_information,
                'sequence_number' => 237,
            ], [
                'module_id' => $module_id,
                'permission_description' => 'Edit Contracts Amendments',
                'created_at' => '2021-03-31 12:22:00',
                'permission_id' => $edit_contract_amendment_information,
                'sequence_number' => 238,
            ]
        ]);
    }

    public function getPermissionId($permission_name)
    {
        $permission_id = CustomPermission::where('name', $permission_name)->value('id');
        return $permission_id;
    }
}
