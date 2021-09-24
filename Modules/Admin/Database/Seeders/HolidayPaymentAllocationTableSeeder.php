<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class HolidayPaymentAllocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('holiday_payment_allocations')->delete();
        
        \DB::table('holiday_payment_allocations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'paymentstatus' => 'Paid if Worked (Stat Rate)',
                'status' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'paymentstatus' => 'Paid (Not Worked - Normal Rate)',
                'status' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'paymentstatus' => 'Not Paid',
                'status' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'paymentstatus' => 'Exempt',
                'status' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            )
            
        ));
    }
}
