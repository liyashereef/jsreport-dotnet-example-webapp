<?php

namespace Modules\Facility\Database\Seeders;

use App\Models\CustomPermission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModulePermissionsScheduleviewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_facilityscheduleview = $this->getPermissionId('view_facilityscheduleview');


        $module_id = \App\Services\HelperService::getModuleId('Facility Booking');
         \DB::table('module_permissions')->insert([
             [
                 'module_id' => $module_id,
                 'permission_description' => 'View Facility Schedule',
                 'created_at' => '2019-07-25 12:22:00',
                 'permission_id' => $view_facilityscheduleview,
                 'sequence_number' => 1,
             ],
         ]);
     }

     public function getPermissionId($permission_name)
     {
         $permission_id = CustomPermission::where('name', $permission_name)->value('id');
         return $permission_id;
     }
}
