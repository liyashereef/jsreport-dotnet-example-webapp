<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OnlineStripeIdsPaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stripe = \DB::table('ids_payment_methods')->where('short_name','STRIPE')->first();
        if(empty($stripe)){
            \DB::table('ids_payment_methods')->insert(array(
                0 => array(
                    'short_name' => 'STRIPE',
                    'full_name' => 'Online-Stripe',
                    'active' => 1,
                    'not_removable'=>1,
                    'created_at' => '2021-06-04 01:35:55',
                    'updated_at' => '2021-06-04 01:35:55',
                )
            ));
        }
    }
}
