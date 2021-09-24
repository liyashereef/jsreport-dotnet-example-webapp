<?php

namespace Modules\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserPaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('user_payment_methods')->delete();

        \DB::table('user_payment_methods')->insert(
            array(
                0 => array(
                    'id' => 1,
                    'payment_methods' => 'Direct Deposit',
                    'apogee_code' => '0',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
                1 => array(
                    'id' => 2,
                    'payment_methods' => 'Cheque',
                    'apogee_code' => '1',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'deleted_at' => null,
                ),
            )
        );
    }
}
