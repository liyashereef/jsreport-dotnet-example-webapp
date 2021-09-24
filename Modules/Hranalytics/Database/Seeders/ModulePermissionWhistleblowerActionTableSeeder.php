<?php

namespace Modules\Hranalytics\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionWhistleblowerActionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $edit_whistleblower_entries = HelperService::getPermissionId('edit_whistleblower_entries');
        $module_id = HelperService::getModuleId('HR Analytics');
        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'Edit Whistleblower Entries',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  $edit_whistleblower_entries,
                'sequence_number' => 263,
            ),

        ));
    }
}
