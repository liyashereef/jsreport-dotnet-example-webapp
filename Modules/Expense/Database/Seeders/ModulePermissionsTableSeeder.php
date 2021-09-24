<?php

namespace Modules\Expense\Database\Seeders;

use App\Models\Module;
use App\Services\HelperService;
use App\Services\SeederService;
use Carbon\Carbon;
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
        Model::unguard();
        $module = Module::where('name', '=', 'Expense')->first();

        $modulePermissions = [
            // [
            //     'module_id' => $module->id,
            //     'permission_description' => 'Expense Masters',
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            //     'permission_id' =>  HelperService::getPermissionId('expense_masters'),
            //     'sequence_number' => 1,
            // ],
            [
                'module_id' => $module->id,
                'permission_description' => 'View all expense claim',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  HelperService::getPermissionId('view_all_expense_claim'),
                'sequence_number' => 1,
            ],
            [
                'module_id' => $module->id,
                'permission_description' => 'View allocated expense claim',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  HelperService::getPermissionId('view_allocated_expense_claim'),
                'sequence_number' => 2,
            ],
            [
                'module_id' => $module->id,
                'permission_description' => 'View all mileage claim',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  HelperService::getPermissionId('view_all_mileage_claim'),
                'sequence_number' => 3,
            ],
            [
                'module_id' => $module->id,
                'permission_description' => 'View allocated mileage claim',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  HelperService::getPermissionId('view_allocated_mileage_claim'),
                'sequence_number' => 4,
            ],
            [
                'module_id' => $module->id,
                'permission_description' => 'Expense send statements',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' =>  HelperService::getPermissionId('expense_send_statements'),
                'sequence_number' => 5,
            ]
        ];

        SeederService::seedModulePermissions($modulePermissions);
    }
}
