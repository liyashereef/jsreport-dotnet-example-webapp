<?php

namespace Modules\KeyManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Carbon\Carbon;

class ModulePermissionsViewAllocatedKeyLogSummaryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_allocated_keylog_summary = HelperService::getPermissionId('view_allocated_keylog_summary');
        $module_id = HelperService::getModuleId('Key Management');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View allocated key log summary',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
                'permission_id' => $view_allocated_keylog_summary,
                'sequence_number' => 6,
            )         
        ));
    }
}
