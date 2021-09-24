<?php

namespace Modules\Facility\Database\Seeders;

use App\Models\CustomPermission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_all_customer_facility = $this->getPermissionId('view_all_customer_facility');
        $view_allocated_customer_facility = $this->getPermissionId('view_allocated_customer_facility');
        $manage_all_customer_facility = $this->getPermissionId('manage_all_customer_facility');
        $manage_allocated_customer_facility = $this->getPermissionId('manage_allocated_customer_facility');
        $remove_customer_facility = $this->getPermissionId('remove_customer_facility');
        $manage_all_customer_facility_service = $this->getPermissionId('manage_all_customer_facility_service');
        $remove_customer_facility_service = $this->getPermissionId('remove_customer_facility_service');
        $manage_all_facility_users = $this->getPermissionId('manage_all_facility_users');
        $manage_allocated_facility_users = $this->getPermissionId('manage_allocated_facility_users');
        $remove_facility_users = $this->getPermissionId('remove_facility_users');
        $manage_user_allocation = $this->getPermissionId('manage_user_allocation');

        $module_id = \App\Services\HelperService::getModuleId('Facility Booking');
         \DB::table('module_permissions')->insert([
             [
                 'module_id' => $module_id,
                 'permission_description' => 'View All Facilities',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $view_all_customer_facility,
                 'sequence_number' => 1,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'View Allocated Customers Facilities',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $view_allocated_customer_facility,
                 'sequence_number' => 2,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'Add/Edit All Customers Facility',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $manage_all_customer_facility,
                 'sequence_number' => 3,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'Add/Edit Allocated Customers Facility',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $manage_allocated_customer_facility,
                 'sequence_number' => 4,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'Remove Customers Facility',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $remove_customer_facility,
                 'sequence_number' => 5,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'Add/Edit Facility Service',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $manage_all_customer_facility_service,
                 'sequence_number' => 6,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'Remove Facility Service',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $remove_customer_facility_service,
                 'sequence_number' => 8,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'Add/Edit All Facilities Users',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $manage_all_facility_users,
                 'sequence_number' => 9,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'Add/Edit Allocated Facilities Users',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $manage_allocated_facility_users,
                 'sequence_number' => 10,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'Remove Facility Users',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $remove_facility_users,
                 'sequence_number' => 11,
             ],
             [
                 'module_id' => $module_id,
                 'permission_description' => 'Manage User Allocation',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $manage_user_allocation,
                 'sequence_number' => 12,
             ],
         ]);
     }

     public function getPermissionId($permission_name)
     {
         $permission_id = CustomPermission::where('name', $permission_name)->value('id');
         return $permission_id;
     }
}
