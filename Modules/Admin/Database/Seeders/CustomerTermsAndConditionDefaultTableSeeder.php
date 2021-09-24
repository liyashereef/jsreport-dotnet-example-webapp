<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CustomerTermsAndConditionDefaultTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \DB::table('customer_terms_and_conditions')->delete();
        
        \DB::table('customer_terms_and_conditions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type_id' => 1,
                'customer_id' => 0,
                'terms_and_conditions' => '',
                'created_by' => 1,
                // 'created_at' => '',
                // 'updated_at' => '',
                // 'deleted_at' => NULL,
            )));
    }
}
