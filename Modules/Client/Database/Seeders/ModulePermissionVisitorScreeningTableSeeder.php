<?php

namespace Modules\Client\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Services\HelperService;

class ModulePermissionVisitorScreeningTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_all_customers_in_visitor_screening = HelperService::getPermissionId('view_all_customers_in_visitor_screening');
        $view_allocated_customers_in_visitor_screening = HelperService::getPermissionId('view_allocated_customers_in_visitor_screening');
        $module_id = HelperService::getModuleId('Client');

        \DB::table('module_permissions')->insert(array(
            0 => array(
                'module_id' => $module_id,
                'permission_description' => 'View All Customer In Visitor Screening',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_all_customers_in_visitor_screening,
                'sequence_number' => 225,
            ),
            1 => array(
                'module_id' => $module_id,
                'permission_description' => 'View Allocated Customer In Visitor Screening',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_allocated_customers_in_visitor_screening,
                'sequence_number' => 226,
            ),
        ));
    }
}
