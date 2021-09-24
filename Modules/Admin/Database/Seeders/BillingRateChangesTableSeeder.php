<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BillingRateChangesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('contract_billing_rate_changes')->delete();
        
        \DB::table('contract_billing_rate_changes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'ratechangetitle' => 'Anuually',
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'ratechangetitle' => 'Quarterly',
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
        ));
    }
}
