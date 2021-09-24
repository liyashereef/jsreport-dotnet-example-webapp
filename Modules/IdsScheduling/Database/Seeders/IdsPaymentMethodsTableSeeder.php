<?php

namespace Modules\IdsScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IdsPaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       \DB::table('ids_payment_methods')->truncate();

        \DB::table('ids_payment_methods')->insert(array(
            0 => array(
                'short_name' => 'VI',
                'full_name' => 'Visa',
                'active' => 1,

            ),
            1 => array(
                'short_name' => 'MC',
                'full_name' => 'Mastercard',
                'active' => 1,

            ),
            2 => array(
                'short_name' => 'DC',
                'full_name' => 'Debit Card',
                'active' => 1,

            ),
            3 => array(
                'short_name' => 'CH',
                'full_name' => 'Cash',
                'active' => 1,

            )
        ));
    }
}
