<?php

namespace Modules\Vehicle\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
class ModulePermissionVehicleAnalysisTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_vehicle_analysis = HelperService::getPermissionId('view_vehicle_analysis');
        $module_id = HelperService::getModuleId('Vehicle');
        DB::table('module_permissions')->insert([
            [
                'module_id' => $module_id,
                'permission_description' => 'View Vehicle Analysis',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_vehicle_analysis,
                'sequence_number' => 7,
            ]
        ]);
    }
}
