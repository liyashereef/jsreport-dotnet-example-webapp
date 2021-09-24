<?php

namespace Modules\Expense\Database\Seeders;

use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ModulePermissionExpenseAppSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $view_expense_in_app_id = HelperService::getPermissionId('view_expense_in_app');
        $module_id = \App\Services\HelperService::getModuleId('Expense');

        \DB::table('module_permissions')->where('module_id', $module_id)->where('permission_id',$view_expense_in_app_id)->delete();

            \DB::table('module_permissions')->insert(array(
                0 => array(
                        'module_id' => $module_id,
                        'permission_description' => 'View Expense in App',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'permission_id' => $view_expense_in_app_id,
                        'sequence_number' => 10,
                    ),

        ));
        }
    

}
