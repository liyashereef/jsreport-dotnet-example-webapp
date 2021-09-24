<?php

namespace Modules\Expense\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ExpenseStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('expense_status')->delete();
        
        \DB::table('expense_status')->insert(array (
            0 => 
            array (
                'id' => 1,
                'status' => 'Pending',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
            ),
            1 => 
            array (
                'id' => 2,
                'status' => 'Rejected',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
            ),
            2 => 
            array (
                'id' => 3,
                'status' => 'Approved',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
            ),
            3 => 
            array (
                'id' => 4,
                'status' => 'Pending Reimbursement',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
            ),
            4 => 
            array (
                'id' => 5,
                'status' => 'Reimbursed',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
            ),
        ));
    }
}
