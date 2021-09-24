<?php

namespace Modules\Expense\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ExpenseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

         $this->call(ModuleTableSeeder::class);
         $this->call(RoleAndPermissionsTableSeeder::class);
         $this->call(ModulePermissionsTableSeeder::class);
         $this->call(ExpenseParentCategoryTableSeeder::class);
         $this->call(ExpenseStatusTableSeeder::class);
         $this->call(ExpenseSettingsSentAttachmentTableSeeder::class);
         $this->call(RolesAndPermissionExpenseAppSeeder::class);
         $this->call(ModulePermissionExpenseAppSeeder::class);
         
    }
}