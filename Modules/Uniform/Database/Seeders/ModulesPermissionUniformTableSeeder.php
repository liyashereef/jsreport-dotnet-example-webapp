<?php

namespace Modules\Uniform\Database\Seeders;

use App\Services\SeederService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Services\HelperService;

class ModulesPermissionUniformTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $view_ura_balance = HelperService::getPermissionId('view_ura_balance');
        $view_uniform_in_app = HelperService::getPermissionId('view_uniform_in_app');
        $view_ura_transactions = HelperService::getPermissionId('view_ura_transactions');
        $add_ura_debit_transaction = HelperService::getPermissionId('add_ura_debit_transaction');
        $add_ura_credit_transaction = HelperService::getPermissionId('add_ura_credit_transaction');
        $view_uniform_orders = HelperService::getPermissionId('view_uniform_orders');
        $change_uniform_order_status = HelperService::getPermissionId('change_uniform_order_status');

        $module_id = HelperService::getModuleId('Uniform');

        $modulePermissionArr = array(
            array(
                'module_id' => $module_id,
                'permission_description' => 'View Uniform In App',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_uniform_in_app,
                'sequence_number' => 100,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'View Ura Balance',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_ura_balance,
                'sequence_number' => 101,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'View Ura Transactions',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_ura_transactions,
                'sequence_number' => 102,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'Add Ura Debit Transaction',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $add_ura_debit_transaction,
                'sequence_number' => 103,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'Add Ura Credit Transaction',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $add_ura_credit_transaction,
                'sequence_number' => 104,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'View Uniform Orders',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $view_uniform_orders,
                'sequence_number' => 105,
            ),
            array(
                'module_id' => $module_id,
                'permission_description' => 'Change Uniform Order Status',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'permission_id' => $change_uniform_order_status,
                'sequence_number' => 106,
            ),
        );

        SeederService::seedModulePermissions($modulePermissionArr);
    }
}
