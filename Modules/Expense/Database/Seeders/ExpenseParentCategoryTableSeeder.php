<?php

namespace Modules\Expense\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ExpenseParentCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('expense_payment_modes')->delete();

        \DB::table('expense_payment_modes')->insert(array(
            0 => array(
                'id' => 1,
                'mode_of_payment' => 'Cash',
                'deleted_at' => NULL,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'deleted_at' => NULL,

            ),
            1 => array(
                'id' => 2,
                'mode_of_payment' => 'Debit Card',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'deleted_at' => NULL,
            ),
            2 => array(
                'id' => 3,
                'mode_of_payment' => 'Credit Card',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'deleted_at' => NULL,
            ),
            3 => array(
                'id' => 4,
                'mode_of_payment' => 'Corporate Credit Card',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'deleted_at' => NULL,
            )

        ));
    }
}
